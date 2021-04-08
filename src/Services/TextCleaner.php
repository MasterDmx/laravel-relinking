<?php

namespace MasterDmx\LaravelRelinking\Services;

class TextCleaner
{
    public function clear(string $text): string
    {
        // Убираем HTML теги
        $text = strip_tags($text);

        // Убираем квадратные скобки и их содержимое
        $text = preg_replace('/\[.*?\]/', '', $text);

        // Убираем фигурные скобки и их содержимое
        $text = preg_replace('/\{.*?\}/', '', $text);

        // Убираем знаки препинания
        $text = preg_replace("/(?![=$'€%-])\p{P}/u", "", $text);

        // Удаляем лишние пробелы
        $text = trim(preg_replace('/\s+/', ' ', $text));

        // Весь текст в нижний регистр
        $text = mb_strtolower($text, 'UTF-8');

        return $text;
    }
}
