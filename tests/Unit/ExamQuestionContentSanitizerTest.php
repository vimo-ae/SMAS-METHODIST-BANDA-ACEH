<?php

use App\Support\AnnouncementContentSanitizer;
use Tests\TestCase;

uses(TestCase::class);

it('preserves superscript, subscript, and tables in exam question content', function () {
    $html = app(AnnouncementContentSanitizer::class)->sanitizeExamQuestion(
        '<p>x<sup>2</sup> + H<sub>2</sub>O</p><figure class="table"><table><tbody><tr><td>Cell</td></tr></tbody></table></figure>'
    );

    expect($html)
        ->toContain('<sup>2</sup>')
        ->toContain('<sub>2</sub>')
        ->toContain('<table>')
        ->toContain('<td>Cell</td>');
});
