<?php

declare(strict_types=1);

namespace Plenta\ContaoGithubReleases\Controller\Contao\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: self::TYPE, category: 'texts')]
class GithubReleasesController extends AbstractContentElementController
{
    public const TYPE = 'customer-content-element';

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->fetchGitHubReleases();

        return $template->getResponse();
    }

    protected function fetchGitHubReleases()
    {
        $url = 'https://api.github.com/repos/plenta/contao-jobs-basic-bundle/releases';

        // Initialize a cURL session
        $ch = curl_init();

        // Set the URL and other options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP'); // GitHub API requires a User-Agent header

        // Execute the request
        $response = curl_exec($ch);

        // Check if any error occurred
        if (curl_errno($ch)) {
            echo "Service nicht erreichbar";
            curl_close($ch);
            return;
        }

        // Check the HTTP status code
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code != 200) {
            echo "Service nicht erreichbar";
            curl_close($ch);
            return;
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the JSON response
        $releases = json_decode($response, true);

        // Check if JSON decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Fehler beim Verarbeiten der JSON-Daten";
            return;
        }

        // Output the desired nodes
        foreach ($releases as $release) {
            echo "Tag Name: " . $release['tag_name'] . "\n";
            echo "Published At: " . $release['published_at'] . "\n";
            echo "Body: " . $release['body'] . "\n";
            echo "\n";
        }
    }
}
