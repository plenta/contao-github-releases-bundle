<?php

declare(strict_types=1);

namespace Plenta\ContaoGithubReleases\Controller\Contao\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Date;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: self::TYPE, category: 'texts')]
class GithubReleasesController extends AbstractContentElementController
{
    public const TYPE = 'github-releases-content-element';

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->empty = 'Nix da';
        $template->items = $this->fetchGitHubReleases();

        return $template->getResponse();
    }

    protected function fetchGitHubReleases(): ?array
    {
        global $objPage;

        $url = 'https://api.github.com/repos/plenta/contao-jobs-basic-bundle/releases';

        $ch = curl_init();

        // Set the URL and other options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP');

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return null;
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code != 200) {
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        $releases = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        $items = [];

        foreach ($releases as $release) {
            $items[] = [
                'tag' => $release['tag_name'],
                'publishedDate' => $release['published_at'],
                'note' => $release['body'],
            ];
        }

        return $items;
    }
}
