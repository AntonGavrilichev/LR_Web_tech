<?php
namespace App\Core;

class Paginator {
    const PER_PAGE = 5;

    public static function generate($currentPage, $totalPages, $urlPattern = '?page=') {
        if ($totalPages <= 1) {
            return '';
        }

        $html = '<div class="pagination">Страницы: ';

        // Всегда показываем первую страницу
        if ($currentPage > 4) {
            $html .= '<a href="' . $urlPattern . '1">1</a> ... ';
        }

        // Определяем диапазон страниц для показа
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $currentPage + 2);

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $currentPage) {
                $html .= '<span class="current">' . $i . '</span> ';
            } else {
                $html .= '<a href="' . $urlPattern . $i . '">' . $i . '</a> ';
            }
        }

        // Всегда показываем последнюю страницу
        if ($currentPage < $totalPages - 2) {
            $html .= '... <a href="' . $urlPattern . $totalPages . '">' . $totalPages . '</a>';
        }

        $html .= '</div>';

        return $html;
    }
}
?><?php
