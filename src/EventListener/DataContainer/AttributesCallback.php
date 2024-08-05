<?php

declare(strict_types=1);

/**
 * Plenta Contao Github Releases
 *
 * @copyright     Copyright (c) 2024, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoGithubReleases\EventListener\DataContainer;

use Contao\DataContainer;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCallback('tl_content', 'fields.url.attributes')]
class AttributesCallback
{
    public function __construct(
        protected readonly TranslatorInterface $translator
    ) {
    }

    public function __invoke(array $attributes, DataContainer|null $dc = null): array
    {
        if (!$dc || 'github-releases-content-element' !== ($dc->getCurrentRecord()['type'] ?? null)) {
            return $attributes;
        }

        $attributes['label'] = $this->translator->trans('tl_content.url.0', [], 'contao_default');;
        $attributes['description'] = $this->translator->trans('tl_content.url.1', [], 'contao_default');;
        $attributes['dcaPicker'] = false;

        return $attributes;
    }
}
