<?php

declare(strict_types=1);

/**
 * @package       Customer
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @license       commercial
 */

use Plenta\ContaoGithubReleases\Controller\Contao\ContentElement\GithubReleasesController;

$GLOBALS['TL_DCA']['tl_content']['palettes'][GithubReleasesController::TYPE] =
    '
    {type_legend},type,headline;
    '
;

dump($GLOBALS['TL_DCA']['tl_content']['fields']);
