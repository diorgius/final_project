<?php

namespace Tests\Unit\Statistics;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\StatisticController;

/**
 * Тесты получения диапазонов дат
 */
class StatisticsPeriodTest extends TestCase
{
    // тест получения текущего дня
    public function test_get_date_for_day(): void
    {
        $controller = new StatisticController();

        [$start, $end] = $controller->getDate('day');

        $this->assertTrue($start->isStartOfDay());
        $this->assertTrue($end->isEndOfDay());
    }

    // тест получения текущего месяца
    public function test_get_date_for_month(): void
    {
        $controller = new StatisticController();

        [$start, $end] = $controller->getDate('month');

        $this->assertTrue($start->isStartOfMonth());
        $this->assertTrue($end->isEndOfMonth());
    }

    // тест получения текущего года
    public function test_get_date_for_year(): void
    {
        $controller = new StatisticController();

        [$start, $end] = $controller->getDate('year');

        $this->assertTrue($start->isStartOfYear());
        $this->assertTrue($end->isEndOfYear());
    }

    // // тест получения всего периода с 01.01.1970
    public function test_get_date_for_all(): void
    {
        $controller = new StatisticController();

        [$start, $end] = $controller->getDate('all');

        $this->assertEquals('1970-01-01', $start->toDateString());
        $this->assertTrue($end->isSameDay(now()));
    }
}
