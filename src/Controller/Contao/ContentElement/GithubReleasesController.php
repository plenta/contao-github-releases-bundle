<?php

declare(strict_types=1);

namespace Plenta\ContaoGithubReleases\Controller\Contao\ContentElement;

use Contao\Config;
use Contao\Date;
use Contao\ContentModel;
use Symfony\Component\HttpClient\HttpClient;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;

#[AsContentElement(type: self::TYPE, category: 'texts')]
class GithubReleasesController extends AbstractContentElementController
{
    public const TYPE = 'github-releases-content-element';

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->empty = 'Nix da';
        $template->items = $this->fetchGitHubReleases('plenta/contao-jobs-basic-bundle');

        return $template->getResponse();
    }

    protected function fetchGitHubReleases(string $repo): ?array
    {
        $client = HttpClient::create();

        try {
            $items = [];
            $response = $client->request('GET', 'https://api.github.com/repos/'.$repo.'/releases', [
                'headers' => [
                    'User-Agent' => 'PHP'
                ]
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                return null;
            }

            $content = $response->getContent();
            $releases = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            foreach ($releases as $release) {
                $items[] = [
                    'tag' => $release['tag_name'],
                    'publishedDate' => Date::parse(Config::get('datimFormat'), $release['published_at']),
                    'note' => $release['body'],
                    'url' => $release['html_url'],
                ];
            }

            return $items;
        } catch (TransportExceptionInterface | ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            return null;
        }
    }
}
