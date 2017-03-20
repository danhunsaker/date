<?php

use Carbon\Carbon;
use Jenssegers\Date\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function setUp()
    {
        date_default_timezone_set('UTC');
        Date::setLocale('en');
    }

    public function testConstructs()
    {
        $date = new Date;
        $this->assertInstanceOf('Jenssegers\Date\Date', $date);
    }

    public function testStaticNow()
    {
        $date = Date::now();
        $this->assertInstanceOf('Jenssegers\Date\Date', $date);
        $this->assertEquals(time(), $date->getTimestamp());
    }

    public function testConstructFromString()
    {
        $date = new Date('2013-01-31');
        $this->assertSame(1359590400, $date->getTimestamp());

        $date = new Date('1 day ago');
        $this->assertSame(time() - 86400, $date->getTimestamp());
    }

    public function testConstructWithTimezone()
    {
        $date = new Date('now', 'Europe/Paris');
        date_default_timezone_set('Europe/Paris');
        $this->assertSame(time(), $date->getTimestamp());

        date_default_timezone_set('Europe/Brussels');

        $date = new Date(null, 'Europe/Paris');
        date_default_timezone_set('Europe/Paris');
        $this->assertSame(time(), $date->getTimestamp());
    }

    public function testConstructTimestamp()
    {
        $date = new Date(1367186296);
        $this->assertSame(1367186296, $date->getTimestamp());
    }

    public function testMake()
    {
        $date1 = Date::make('Sunday 28 April 2013 21:58:16');
        $date2 = new Date('Sunday 28 April 2013 21:58:16');
        $this->assertEquals($date1, $date2);
    }

    public function testCreateFromCarbon()
    {
        $date = Date::make(Carbon::createFromFormat('U', 1367186296));
        $this->assertInstanceOf('Jenssegers\Date\Date', $date);
        $this->assertEquals(1367186296, $date->getTimestamp());
    }

    public function testManipulation()
    {
        $this->assertInstanceOf('Jenssegers\Date\Date', Date::now()->add('1 day'));
        $this->assertInstanceOf('Jenssegers\Date\Date', Date::now()->sub('1 day'));

        $this->assertSame(86400, Date::now()->add('1 day')->getTimestamp() - Date::now()->getTimestamp());
        $this->assertSame(4 * 86400, Date::now()->add('4 day')->getTimestamp() - Date::now()->getTimestamp());

        $this->assertSame(-86400, Date::now()->sub('1 day')->getTimestamp() - Date::now()->getTimestamp());
        $this->assertSame(-4 * 86400, Date::now()->sub('4 day')->getTimestamp() - Date::now()->getTimestamp());

        $this->assertSame(10 * 86400, Date::now()->add('P10D')->getTimestamp() - Date::now()->getTimestamp());
        $this->assertSame(-10 * 86400, Date::now()->sub('P10D')->getTimestamp() - Date::now()->getTimestamp());
    }

    public function testFormat()
    {
        $date = new Date(1367186296);
        $this->assertSame('Sunday 28 April 2013 21:58:16', $date->format('l j F Y H:i:s'));
    }

    public function testAge()
    {
        $date = Date::parse('-5 years');
        $this->assertSame(5, $date->age);
    }

    public function testAgo()
    {
        $date = Date::parse('-5 years');
        $this->assertSame('5 years ago', $date->ago());

        $date = Date::parse('-5 months');
        $this->assertSame('5 months ago', $date->ago());

        $date = Date::parse('-32 days');
        $this->assertSame('1 month ago', $date->ago());

        $date = Date::parse('-4 days');
        $this->assertSame('4 days ago', $date->ago());

        $date = Date::parse('-1 day');
        $this->assertSame('1 day ago', $date->ago());

        $date = Date::parse('-3 hours');
        $this->assertSame('3 hours ago', $date->ago());

        $date = Date::parse('-1 hour');
        $this->assertSame('1 hour ago', $date->ago());

        $date = Date::parse('-2 minutes');
        $this->assertSame('2 minutes ago', $date->ago());

        $date = Date::parse('-1 minute');
        $this->assertSame('1 minute ago', $date->ago());

        $date = Date::parse('-50 second');
        $this->assertSame('50 seconds ago', $date->ago());

        $date = Date::parse('-1 second');
        $this->assertSame('1 second ago', $date->ago());

        $date = Date::parse('+5 days');
        $this->assertSame('5 days from now', $date->ago());

        $date = Date::parse('+5 days');
        $this->assertSame('5 days after', $date->ago(Date::now()));

        $date = Date::parse('-5 days');
        $this->assertSame('5 days before', $date->ago(Date::now()));
    }

    public function testAbsoluteAgo()
    {
        $date = Date::parse('-5 days');
        $this->assertSame('5 days', $date->ago(Date::now(), true));

        $date = Date::parse('+5 days');
        $this->assertSame('5 days', $date->ago(Date::now(), true));
    }

    public function testDiffForHumans()
    {
        $date = Date::parse('-5 years');
        $this->assertSame('5 years ago', $date->diffForHumans());

        $date = Date::parse('-15 days');
        $this->assertSame('2 weeks ago', $date->diffForHumans());

        $date = Date::parse('-13 days');
        $this->assertSame('1 week ago', $date->diffForHumans());

        $date = Date::parse('-13 days');
        $this->assertSame('1 week', $date->diffForHumans(null, true));

        $date = Date::parse('-3 months');
        $this->assertSame('3 months', $date->diffForHumans(null, true));

        $date = Date::parse('-1 week');
        $future = Date::parse('+1 week');
        $this->assertSame('2 weeks after', $future->diffForHumans($date));
        $this->assertSame('2 weeks before', $date->diffForHumans($future));
    }

    public function testTimespan()
    {
        $date = new Date(1403619368);
        $date = $date->sub('-100 days -3 hours -20 minutes');

        $this->assertSame('3 months, 1 week, 1 day, 3 hours, 20 minutes', $date->timespan(1403619368));
    }

    public function testTranslateTimeString()
    {
        Date::setLocale('ru');
        $date = Date::translateTimeString('понедельник 21 март 2015');
        $this->assertSame('monday 21 march 2015', $date);

        Date::setLocale('de');
        $date = Date::translateTimeString('Montag 21 März 2015');
        $this->assertSame('monday 21 march 2015', $date);
    }

    public function testToLocaleDateTimeString()
    {
        $date = Date::now();

        Date::setLocale('en');
        $this->assertSame($date->format('D M j H:i:s Y'), $date->toLocaleDateTimeString());

        Date::setLocale('en_US');
        $this->assertSame($date->format('D d M Y h:i:s A T'), $date->toLocaleDateTimeString());

        Date::setLocale('en_GB');
        $this->assertSame($date->format('D d M Y H:i:s T'), $date->toLocaleDateTimeString());

        Date::setLocale('ru');
        $this->assertSame($date->format('D d M Y H:i:s'), $date->toLocaleDateTimeString());

        Date::setLocale('de');
        $this->assertSame($date->format('D d M Y H:i:s T'), $date->toLocaleDateTimeString());
    }

    public function testToLocaleDateString()
    {
        $date = Date::now();

        Date::setLocale('en');
        $this->assertSame($date->format('m/d/y'), $date->toLocaleDateString());

        Date::setLocale('en_US');
        $this->assertSame($date->format('m/d/Y'), $date->toLocaleDateString());

        Date::setLocale('en_GB');
        $this->assertSame($date->format('d/m/y'), $date->toLocaleDateString());

        Date::setLocale('ru');
        $this->assertSame($date->format('d.m.Y'), $date->toLocaleDateString());

        Date::setLocale('de');
        $this->assertSame($date->format('d.m.Y'), $date->toLocaleDateString());
    }

    public function testToLocaleTimeString()
    {
        $date = Date::now();

        Date::setLocale('en');
        $this->assertSame($date->format('H:i:s'), $date->toLocaleTimeString());

        Date::setLocale('en_US');
        $this->assertSame($date->format('h:i:s A'), $date->toLocaleTimeString());

        Date::setLocale('en_GB');
        $this->assertSame($date->format('H:i:s'), $date->toLocaleTimeString());

        Date::setLocale('ru');
        $this->assertSame($date->format('H:i:s'), $date->toLocaleTimeString());

        Date::setLocale('de');
        $this->assertSame($date->format('H:i:s'), $date->toLocaleTimeString());
    }
}
