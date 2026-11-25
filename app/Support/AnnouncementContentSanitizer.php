<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

class AnnouncementContentSanitizer
{
    /** @var list<string> */
    private const ALLOWED_ELEMENTS = [
        'a',
        'blockquote',
        'br',
        'code',
        'em',
        'h2',
        'h3',
        'img',
        'li',
        'ol',
        'p',
        'pre',
        's',
        'strong',
        'u',
        'ul',
    ];

    /** @var list<string> */
    private const EXAM_QUESTION_ELEMENTS = [
        'a',
        'blockquote',
        'br',
        'div',
        'em',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'img',
        'li',
        'ol',
        'p',
        's',
        'strong',
        'table',
        'tbody',
        'td',
        'tfoot',
        'th',
        'thead',
        'tr',
        'u',
        'ul',
    ];

    /** @var list<string> */
    private const ELEMENTS_REMOVED_WITH_CONTENT = [
        'button',
        'embed',
        'form',
        'iframe',
        'input',
        'link',
        'math',
        'meta',
        'object',
        'script',
        'select',
        'style',
        'svg',
        'textarea',
    ];

    public function sanitize(string $content): string
    {
        return $this->sanitizeContent($content, self::ALLOWED_ELEMENTS, 'announcement-images');
    }

    public function sanitizeExamQuestion(string $content): string
    {
        return $this->sanitizeContent($content, self::EXAM_QUESTION_ELEMENTS, 'exam-questions');
    }

    /**
     * @param list<string> $allowedElements
     */
    private function sanitizeContent(
        string $content,
        array $allowedElements,
        string $imageDirectory
    ): string {
        if (trim($content) === '') {
            return '';
        }

        if ($content === strip_tags($content)) {
            return nl2br(htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrorState = libxml_use_internal_errors(true);

        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="announcement-content-root">'.$content.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorState);

        $root = (new DOMXPath($document))->query('//*[@id="announcement-content-root"]')->item(0);

        if (! $root instanceof DOMElement) {
            return '';
        }

        $this->sanitizeChildren($root, $allowedElements, $imageDirectory);

        $sanitizedContent = '';

        foreach (iterator_to_array($root->childNodes) as $child) {
            $sanitizedContent .= $document->saveHTML($child);
        }

        return trim($sanitizedContent);
    }

    public function hasMeaningfulContent(string $content): bool
    {
        $textContent = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim($textContent) !== '' || str_contains($content, '<img');
    }

    private function sanitizeChildren(
        DOMNode $parent,
        array $allowedElements = self::ALLOWED_ELEMENTS,
        string $imageDirectory = 'announcement-images'
    ): void
    {
        foreach (iterator_to_array($parent->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                if ($child->nodeType === XML_COMMENT_NODE) {
                    $parent->removeChild($child);
                }

                continue;
            }

            $tagName = strtolower($child->tagName);

            if (in_array($tagName, self::ELEMENTS_REMOVED_WITH_CONTENT, true)) {
                $parent->removeChild($child);

                continue;
            }

            if (! in_array($tagName, $allowedElements, true)) {
                $this->sanitizeChildren($child, $allowedElements, $imageDirectory);
                $this->unwrapElement($child);

                continue;
            }

            if (! $this->sanitizeAttributes($child, $tagName, $imageDirectory)) {
                $parent->removeChild($child);

                continue;
            }

            $this->sanitizeChildren($child, $allowedElements, $imageDirectory);
        }
    }

    private function sanitizeAttributes(
        DOMElement $element,
        string $tagName,
        string $imageDirectory
    ): bool
    {
        $attributes = [];

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $attributes[strtolower($attribute->name)] = $attribute->value;
            $element->removeAttributeNode($attribute);
        }

        if ($tagName === 'a') {
            $href = trim($attributes['href'] ?? '');

            // Tambahkan https:// jika tidak ada skema (mis. "google.com", "www.google.com").
            // Skema berbahaya seperti "javascript:" tetap dikenali sebagai skema,
            // sehingga tidak diberi awalan dan akan ditolak oleh isSafeLink().
            if ($href !== '' && ! preg_match('/^([a-z][a-z0-9+.\-]*:|\/|#)/i', $href)) {
                $href = 'https://'.$href;
            }

            if (! $this->isSafeLink($href)) {
                return true;
            }

            $element->setAttribute('href', $href);
            $element->setAttribute('target', '_blank');
            $element->setAttribute('rel', 'noopener noreferrer');

            if (isset($attributes['title'])) {
                $element->setAttribute('title', $attributes['title']);
            }
        }

        if ($tagName === 'img') {
            $source = trim($attributes['src'] ?? '');

            if (! $this->isAllowedImageSource($source, $imageDirectory)) {
                return false;
            }

            $element->setAttribute('src', $source);
            $element->setAttribute(
                'alt',
                $attributes['alt'] ?? ($imageDirectory === 'exam-questions' ? 'Gambar soal' : 'Gambar pengumuman')
            );
            $element->setAttribute('loading', 'lazy');
        }

        if (in_array($tagName, ['td', 'th'], true)) {
            foreach (['colspan', 'rowspan'] as $spanAttribute) {
                if (isset($attributes[$spanAttribute]) && ctype_digit($attributes[$spanAttribute])) {
                    $element->setAttribute($spanAttribute, $attributes[$spanAttribute]);
                }
            }
        }

        if ($tagName === 'li' && in_array($attributes['data-list'] ?? '', ['bullet', 'ordered'], true)) {
            $element->setAttribute('data-list', $attributes['data-list']);
        }

        return true;
    }

    private function isSafeLink(string $href): bool
    {
        if ($href === '' || preg_match('/[\x00-\x20\x7f]/u', $href) === 1) {
            return false;
        }

        $scheme = strtolower((string) parse_url($href, PHP_URL_SCHEME));

        return $scheme === ''
            ? str_starts_with($href, '/') || str_starts_with($href, '#')
            : in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }

    private function isAllowedImageSource(string $source, string $imageDirectory): bool
    {
        if ($source === '' || preg_match('/[\x00-\x20\x7f]/u', $source) === 1) {
            return false;
        }

        if (
            $imageDirectory === 'exam-questions'
            && preg_match('/^data:image\/(?:png|jpeg|webp);base64,[a-z0-9+\/]+=*$/i', $source) === 1
        ) {
            return true;
        }

        $path = (string) parse_url($source, PHP_URL_PATH);

        if (! str_starts_with($path, '/storage/'.$imageDirectory.'/')) {
            return false;
        }

        $host = parse_url($source, PHP_URL_HOST);
        $applicationHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        return $host === null || $applicationHost === null || strcasecmp($host, $applicationHost) === 0;
    }

    private function unwrapElement(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if ($parent === null) {
            return;
        }

        while ($element->firstChild !== null) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }
}