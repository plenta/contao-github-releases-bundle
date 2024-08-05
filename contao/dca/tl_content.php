<?php

declare(strict_types=1);

/**
 * Plenta Contao Github Releases
 *
 * @copyright     Copyright (c) 2024, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Plenta\ContaoGithubReleases\Controller\Contao\ContentElement\GithubReleasesController;

$GLOBALS['TL_DCA']['tl_content']['palettes'][GithubReleasesController::TYPE] =
    '
    {type_legend},type,headline;
    {github_legend},url;
    '
;
