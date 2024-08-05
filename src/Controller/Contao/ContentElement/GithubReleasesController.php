<?php

declare(strict_types=1);

/**
 * Plenta Contao Github Releases
 *
 * @copyright     Copyright (c) 2024, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoGithubReleases\Controller\Contao\ContentElement;

use Contao\Config;
use Contao\ContentModel;
use Symfony\Component\HttpClient\HttpClient;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
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

    public function __construct(
        protected readonly TranslatorInterface $translator
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->empty = $this->translator->trans('MSC.PLENTA_GITHUB_RELEASES.error', [], 'contao_default');
        $template->items = $this->fetchGitHubReleases($model->url);

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
                $dateTime = new \DateTime($release['published_at']);
                $items[] = [
                    'tag' => $release['tag_name'],
                    'publishedDate' => $dateTime->format(Config::get('dateFormat')),
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
