<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class WebCrawlerCommand extends Command
{
    protected static $defaultName = 'app:basic-html-crawl';

    private EntityManagerInterface $em;
    private ContainerInterface $container;
    private Crawler $crawler;

    const CRAWLER_TYPE_TEXT = 'text';
    const CRAWLER_TYPE_HTML = 'html';

    public function __construct(EntityManagerInterface $em,
                                ContainerInterface $container
                                )
    {
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Basic Web Page crawler');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Attempting crawl data...');

        $rowProcessed = 0;
        $index = 0;
        try {
            $io->writeln('');
            $io->writeln('Processing row n° '.$index);

            $client = HttpClient::create();
            $url = sprintf("http://www.google.com", $index);
            $io->writeln('URL: '.$url);
            $response = $client->request('GET', $url);
            if ($response->getStatusCode() === Response::HTTP_OK) {
                $io->writeln(sprintf('Row n° %d, http code %d', $index, $response->getStatusCode()));

                $this->crawler = new Crawler($response->getContent());

                $data = [];
                // Retrieve elements
                $data['en-name'] = $this->getCrawlerValue('.poi-name-container > div.smaller-font-name');
                $data['local-name'] = $this->getCrawlerValue('span[itemprop="name"]');
                $data['pricing'] = $this->getCrawlerValue('.header-poi-price > a');
                $data['rating'] = $this->getCrawlerValue('.header-score');
                $data['district'] = $this->getCrawlerValue('.header-poi-district > a');
                $data['review-number'] = $this->getCrawlerValue('span[itemprop="reviewCount"]');
                $data['liked-number'] = $this->crawler->filter('.header-smile-section > .score-div')->first()->text();
                $data['ok-number'] = $this->getCrawlerValue('.header-smile-section > .score-div:nth-child(4)');
                $data['en-address'] = $this->getCleanAddress('.address-info-section .content a');
                $data['local-address'] = $this->getCleanAddress('.address-section > div.content a');
                $data['phone'] = $this->getCrawlerValue('.telephone-section div.content');
                $data['payment-methods'] = $this->getCrawlerValue('div.comma-tags', self::CRAWLER_TYPE_HTML);
                $data['types'] = $this->getCrawlerValue('.header-poi-categories', self::CRAWLER_TYPE_HTML);
                $data['website'] = $this->getCrawlerValue('.restaurant-url-section a');
                $data['other-infos'] = $this->getCrawlerValue('.conditions-section > div:nth-of-type(2)', self::CRAWLER_TYPE_HTML);
                $data['opening-hours'] = $this->getCrawlerValue('.opening-hours-container', self::CRAWLER_TYPE_HTML);
                $data['all-photos-link'] = $this->getCrawlerValue('.photos li.item:nth-of-type(3) a');
                $data['all-photos-link-href'] = $this->getLink('.photos li.item:nth-of-type(3) a');
                $data['area'] = $this->getCrawlerValue('li:nth-of-type(3) span');
                $data['place'] = $this->getCrawlerValue('.breadcrumb li:nth-of-type(5)');
                $data['is-closed'] = $this->getCrawlerValue('span.poi-with-other-status');
            } else {
                $io->writeln(sprintf('Row n° %d, http code %d', $index, $response->getStatusCode()));
            }
        } catch (TransportException $e) {
            $io->writeln('');
            $io->writeln(sprintf('TransportException: %s', $e->getMessage()));
            $io->writeln(sprintf('Retry for index n° %d ...', $index));
        }

        $io->success(sprintf('Successfully crawled %d rows(s)!', $rowProcessed));
        return 0;
    }

    private function getCleanAddress(string $filter): string
    {
        if (!$this->crawler->filter($filter)->count()) {
            return '';
        }

        $data = preg_replace('/\s+/', ' ', $this->crawler->filter($filter)->text());

        return trim(urldecode(str_replace('+%EF%BB%BF+', '', urlencode($data))));
    }

    private function getCrawlerValue(string $filter, string $type = self::CRAWLER_TYPE_TEXT): string
    {
        if (!$this->crawler->filter($filter)->count()) {
            return '';
        }

        $data = $this->crawler->filter($filter);
        return ($type === self::CRAWLER_TYPE_TEXT) ? trim($data->text()) : trim($data->html());
    }

    private function getLink($elementPath): string
    {
        if (!$this->crawler->filter($elementPath)->count()) {
            return '';
        }
        $urlPath = $this->crawler->filter($elementPath)->attr('href');

        return $urlPath;
    }
}
