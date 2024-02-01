<?php

namespace App\Tests\Twig;

use App\Twig\Extension\AppExtension;
use PHPUnit\Framework\TestCase;

class SluggerTest extends TestCase
{
    /**
     * @dataProvider getSlugs
     */
    public function testSlugify(string $string, string $slug): void
    {
        $slugger = new AppExtension;
        $this->assertSame($slug,$slugger->slugify($string));
    }

    public function getSlugs()
    {
            yield ['Hello World','hello-world'];
            yield [' Hello World','hello-world'];
            yield ['HeLLo WorLd','hello-world'];
            yield ['!Hello World!','hello-world'];
            yield ['hello-world','hello-world'];
            yield ['Children\'s books','childrens-books'];
            yield ['Five star movies','five-star-movies'];
            yield ['Adults 60+','adults-60'];
    }
}
