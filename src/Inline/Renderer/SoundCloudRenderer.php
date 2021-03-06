<?php

namespace JohnnyHuy\Laravel\Inline\Renderer;

use ErrorException;
use JohnnyHuy\Laravel\Inline\Element\SoundCloud;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\AbstractWebResource;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\Util\Configuration;

class SoundCloudRenderer implements InlineRendererInterface
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @param AbstractInline|AbstractWebResource $inline
     * @param ElementRendererInterface $htmlRenderer
     *
     * @return HtmlElement|string
     * @throws ErrorException
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof SoundCloud)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        // Use a oEmbed route to get SoundCloud details
        $url = "https://soundcloud.com/oembed?&format=json&url={$inline->getUrl()}&maxheight=166";
        $soundCloud = $this->getContent($url);

        // Seems that the used SoundCloud url is invalid
        // or SoundCloud is currently not available
        if ($soundCloud === null) {
            throw new ErrorException('SoundCloud request returned null: ' . $url);
        }

        // Parse the embed response
        $soundCloud = json_decode($soundCloud);

        // Use the embed html snippet as response
        return $soundCloud->html;
    }

    /**
     * @param string $url
     * @return string
     */
    public function getContent(string $url): string
    {
        return file_get_contents($url);
    }
}
