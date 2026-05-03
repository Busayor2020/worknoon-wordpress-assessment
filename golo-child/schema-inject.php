<?php
/**
 * Worknoon JSON-LD Schema Injection
 * Outputs Organization, Person, and WebSite schema into <head> on homepage only
 */

function worknoon_inject_schema() {

    if ( ! is_front_page() ) return;

    // ── ORGANIZATION SCHEMA ──────────────────────────────────
    $organization = [
        "@context"    => "https://schema.org",
        "@type"       => "Organization",
        "@id"         => "https://worknoon.com/#organization",
        "name"        => "Worknoon",
        "url"         => "https://worknoon.com",
        "logo"        => [
            "@type" => "ImageObject",
            "url"   => "https://worknoon.laluxuryhair.com/wp-content/uploads/2025/05/Worknoon_Logo_tr-1-scaled.png"
        ],
        "email"       => "info@worknoon.com",
        "address"     => [
            "@type"           => "PostalAddress",
            "streetAddress"   => "3280 Peachtree Rd NE",
            "addressLocality" => "Atlanta",
            "addressRegion"   => "Georgia",
            "postalCode"      => "30305",
            "addressCountry"  => "US"
        ],
        "sameAs" => [
            "https://www.linkedin.com/company/worknoonhq",
            "https://www.instagram.com/worknoon/",
            "https://x.com/worknoon"
        ],
        "founder" => [
            "@type" => "Person",
            "@id"   => "https://worknoon.com/#founder",
            "name"  => "Dr. Yuan Noon"
        ]
    ];

    // ── PERSON SCHEMA ────────────────────────────────────────
    $person = [
        "@context"        => "https://schema.org",
        "@type"           => "Person",
        "@id"             => "https://worknoon.com/#founder",
        "name"            => "Dr. Yuan Noon",
        "honorificPrefix" => "Dr.",
        "jobTitle"        => "Founder & CEO",
        "worksFor"        => [
            "@type" => "Organization",
            "@id"   => "https://worknoon.com/#organization",
            "name"  => "Worknoon"
        ]
    ];

    // ── WEBSITE SCHEMA ───────────────────────────────────────
    $website = [
        "@context"  => "https://schema.org",
        "@type"     => "WebSite",
        "@id"       => "https://worknoon.com/#website",
        "name"      => "Worknoon",
        "url"       => "https://worknoon.com",
        "publisher" => [
            "@type" => "Organization",
            "@id"   => "https://worknoon.com/#organization"
        ],
        "potentialAction" => [
            "@type"       => "SearchAction",
            "target"      => "https://worknoon.com/?s={search_term_string}",
            "query-input" => "required name=search_term_string"
        ]
    ];

    // ── OUTPUT ALL THREE ─────────────────────────────────────
    foreach ( [ $organization, $person, $website ] as $schema ) {
        echo '<script type="application/ld+json">';
        echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
        echo '</script>' . "\n";
    }
}

add_action( 'wp_head', 'worknoon_inject_schema' );