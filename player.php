<?php
// Floating Spotify mini-player. Included by footer.php, so every page gets it.
$spotifyLink = 'https://open.spotify.com/track/37xebnhvUyM8VyyDDeo65d?si=aac9c8131a9845a1';

/** Turn a normal Spotify share link into its embed URL, or null if it is not a valid link. */
function spotify_embed_url(string $link): ?string
{
    $pattern = '~^https://open\.spotify\.com/(?:intl-[a-z]{2}/)?(playlist|album|track|artist|show|episode)/([A-Za-z0-9]{22})~';
    if (!preg_match($pattern, trim($link), $m)) {
        return null;
    }
    return "https://open.spotify.com/embed/{$m[1]}/{$m[2]}?utm_source=generator";
}

$embedUrl = spotify_embed_url($spotifyLink);
if ($embedUrl !== null):
?>
<aside id="music-player" class="music-player" aria-label="Background music">
    <div id="music-panel" class="music-panel">
        <iframe title="Spotify player" src="<?= htmlspecialchars($embedUrl) ?>" width="100%" height="152"
                style="border:0" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"></iframe>
        <p class="music-note">Press play to start the music. <a class="music-open" href="<?= htmlspecialchars(trim($spotifyLink)) ?>" target="_blank" rel="noopener">Open in Spotify</a></p>
    </div>
    <button type="button" class="music-toggle" aria-expanded="true" aria-controls="music-panel">
        <span aria-hidden="true">&#9834;</span> Music
    </button>
</aside>
<script src="js/site.js" defer></script>
<?php endif; ?>