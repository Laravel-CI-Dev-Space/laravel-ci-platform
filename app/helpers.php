<?php

declare(strict_types=1);

if (! function_exists('chatMarkdown')) {
    /**
     * Convertit un texte de réponse IA en HTML sécurisé pour les bulles de chat.
     * Supporte : blocs ```code```, inline `code`, **gras**, retours à la ligne.
     */
    function chatMarkdown(string $text): string
    {
        // 1. Extraire et préserver les blocs de code avant l'échappement
        $codeBlocks = [];
        $text = preg_replace_callback(
            '/```(\w*)\n?(.*?)```/s',
            function ($m) use (&$codeBlocks) {
                $placeholder = "\x02CODE" . count($codeBlocks) . "\x03";
                $lang    = htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8');
                $content = htmlspecialchars($m[2], ENT_QUOTES, 'UTF-8');
                $codeBlocks[$placeholder] = $lang
                    ? "<pre><code class=\"language-{$lang}\">{$content}</code></pre>"
                    : "<pre><code>{$content}</code></pre>";
                return $placeholder;
            },
            $text
        );

        // 2. Échapper le texte restant
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // 3. Inline code : `code`
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);

        // 4. Gras : **texte**
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);

        // 5. Retours à la ligne → <br> (double newline = saut de paragraphe)
        $text = preg_replace('/\n{2,}/', '</p><p>', $text);
        $text = nl2br($text);
        $text = '<p>' . $text . '</p>';

        // 6. Réinjecter les blocs de code
        foreach ($codeBlocks as $placeholder => $html) {
            $text = str_replace(htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8'), $html, $text);
            $text = str_replace($placeholder, $html, $text);
        }

        return $text;
    }
}
