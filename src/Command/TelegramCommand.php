<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

/**
 * @fomat class TelegramConf{ const TOKEN = 'bottoken...'; const CHAT_ID = -111; }
 *
 * @exemple: php bin/console app:telegram --method=sendMessage --params=text='hellowordasddasdassl!!!'
 */
class TelegramCommand extends Command
{
    protected static $defaultName = 'app:telegram';

    protected function configure()
    {
        $this
            ->setDescription('Notification to telegram')
            ->addOption('method', null, InputOption::VALUE_REQUIRED, 'METHOD_NAME to call telegram API')
            ->addOption(
                'params',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'parans to send in method telegram')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = 'https://api.telegram.org/';
        $io = new SymfonyStyle($input, $output);

        $params = 'chat_id=' . TelegramConf::CHAT_ID . '&' . implode('&', $input->getOption('params'));
        $method = $input->getOption('method');
        $url .= TelegramConf::TOKEN . '/' . $method . '?' . $params;
        
        $client = HttpClient::create(['http_version' => '2.0']);
        $response = $client->request('GET', $url);

        if($response->getStatusCode() === 200){
            $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        }else{
            $io->error("code: " . $response->getStatusCode());
        }
    }
}
