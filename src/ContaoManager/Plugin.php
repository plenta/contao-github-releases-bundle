<?php

declare(strict_types=1);

namespace Plenta\ContaoGithubReleases\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Plenta\ContaoGithubReleases\PlentaContaoGithubReleasesBundle;

/**
 * Class ContaoManagerPlugin.
 */
class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(PlentaContaoGithubReleasesBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
