<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class NormalizeArabicPublicCopy
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            app()->getLocale() !== 'ar'
            || $request->is('admin/*')
            || $request->is('login')
            || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')
        ) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return $response;
        }

        /** @var array<string, string> $phrases */
        $phrases = config('arabic-copy.phrases', []);

        uksort($phrases, static fn (string $left, string $right): int => mb_strlen($right) <=> mb_strlen($left));

        $content = str_replace(array_keys($phrases), array_values($phrases), $content);

        $encodedPhrases = json_encode(
            $phrases,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
        );

        if (is_string($encodedPhrases) && str_contains($content, '</body>')) {
            $script = <<<'HTML'
<script id="iprofixer-arabic-copy-normalizer">
(() => {
    const phrases = __PHRASES__;
    const entries = Object.entries(phrases).sort((a, b) => b[0].length - a[0].length);
    const attributes = ['alt', 'aria-label', 'title', 'placeholder'];

    const normalize = (value) => {
        let normalized = value;
        entries.forEach(([source, approved]) => {
            normalized = normalized.split(source).join(approved);
        });
        return normalized;
    };

    const visit = (root) => {
        if (!(root instanceof Node)) return;

        if (root.nodeType === Node.TEXT_NODE && root.nodeValue) {
            root.nodeValue = normalize(root.nodeValue);
            return;
        }

        if (!(root instanceof Element || root instanceof DocumentFragment || root instanceof Document)) return;

        if (root instanceof Element) {
            attributes.forEach((attribute) => {
                if (root.hasAttribute(attribute)) {
                    root.setAttribute(attribute, normalize(root.getAttribute(attribute) ?? ''));
                }
            });
        }

        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT | NodeFilter.SHOW_ELEMENT);
        let node = walker.nextNode();
        while (node) {
            if (node.nodeType === Node.TEXT_NODE && node.nodeValue) {
                node.nodeValue = normalize(node.nodeValue);
            } else if (node instanceof Element) {
                attributes.forEach((attribute) => {
                    if (node.hasAttribute(attribute)) {
                        node.setAttribute(attribute, normalize(node.getAttribute(attribute) ?? ''));
                    }
                });
            }
            node = walker.nextNode();
        }
    };

    visit(document.body);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach(visit);
            if (mutation.type === 'characterData') visit(mutation.target);
        });
    });

    observer.observe(document.body, { childList: true, subtree: true, characterData: true });
})();
</script>
HTML;

            $script = str_replace('__PHRASES__', $encodedPhrases, $script);
            $content = str_replace('</body>', $script.'</body>', $content);
        }

        $response->setContent($content);
        $response->headers->remove('Content-Length');

        return $response;
    }
}
