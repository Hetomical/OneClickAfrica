<?php

namespace Modules\Post\Http\Controllers;

use Sentinel;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Post\Entities\Post;
use Modules\Common\Entities\Cron;
use Illuminate\Routing\Controller;
use Modules\Post\Entities\RssFeed;
use Illuminate\Support\Facades\Log;
use Modules\Post\Entities\Category;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Modules\Post\Entities\SubCategory;
use Aws\S3\Exception\S3Exception as S3;
use Illuminate\Support\Facades\Storage;
use Modules\Language\Entities\Language;
use Modules\Gallery\Entities\Image as galleryImage;

class RssController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $categories     = Category::all();
        $activeLang     = Language::where('status', 'active')->orderBy('name', 'ASC')->get();
        $feeds          = RssFeed::orderBy('id', 'desc')->with('category', 'subCategory')->paginate('15');

        return view('post::rss_feeds', compact('activeLang', 'categories', 'feeds'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function importRss()
    {
        $categories     = Category::where('language', \App::getLocale() ?? settingHelper('default_language'))->get();
        $subCategories  = SubCategory::all();
        $activeLang     = Language::where('status', 'active')->orderBy('name', 'ASC')->get();
        return view('post::import_rss', compact('categories', 'subCategories', 'activeLang'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function saveNewRss(Request $request)
    {
        if (strtolower(\Config::get('app.demo_mode')) == 'yes'):
            return redirect()->back()->with('error', __('You are not allowed to add/modify in demo mode.'));
        endif;
        //        dd($request->all());

        Validator::make($request->all(), [
            'name'              => 'required|min:2',
            'feed_url'          => 'required',
            'language'          => 'required',
            'category_id'       => 'required',
            'post_limit'        => 'required | numeric|max:100'
        ])->validate();

        $rssFeed = new RssFeed();

        try {
            $rssFeed->name          = $request->name;
            $rssFeed->feed_url      = $request->feed_url;
            $rssFeed->language      = $request->language;
            $rssFeed->category_id   = $request->category_id;
            $rssFeed->sub_category_id   = $request->sub_category_id;
            $rssFeed->post_limit        = $request->post_limit;
            $rssFeed->auto_update       = $request->auto_update;
            $rssFeed->show_read_more    = $request->show_read_more;
            $rssFeed->keep_date         = $request->keep_date;
            $rssFeed->status            = $request->status;
            $rssFeed->meta_keywords     = $request->meta_keywords;
            $rssFeed->meta_description  = $request->meta_description;
            $rssFeed->tags              = $request->tags;
            $rssFeed->scheduled_date    = Carbon::parse($request->scheduled_date);
            $rssFeed->layout            = $request->layout;

            $rssFeed->save();

            return redirect()->back()->with('success', __('successfully_added'));
        } catch (\Exception $e) {
            return view('site.pages.500');
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function editRss($id)
    {
        $activeLang     = Language::where('status', 'active')->orderBy('name', 'ASC')->get();
        $feed           = RssFeed::findOrfail($id);
        $categories     = Category::where('language', $feed->language)->get();

        $subCategories  = [];
        if ($feed->category_id != "") {
            $subCategories  = SubCategory::where('category_id', $feed->category['id'])->get();
        }
        return view('post::edit_rss', compact('feed', 'activeLang', 'categories', 'subCategories'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateRss(Request $request, $id)
    {
        if (strtolower(\Config::get('app.demo_mode')) == 'yes'):
            return redirect()->back()->with('error', __('You are not allowed to add/modify in demo mode.'));
        endif;
        Validator::make($request->all(), [
            'name'              => 'required|min:2',
            'feed_url'          => 'required',
            'language'          => 'required',
            'category_id'       => 'required',
            'post_limit'        => 'required | numeric|max:100'
        ])->validate();

        $rssFeed = RssFeed::findOrfail($id);

        try {
            $rssFeed->name          = $request->name;
            $rssFeed->feed_url      = $request->feed_url;
            $rssFeed->language      = $request->language;
            $rssFeed->category_id   = $request->category_id;
            $rssFeed->sub_category_id   = $request->sub_category_id;
            $rssFeed->post_limit        = $request->post_limit;
            $rssFeed->auto_update       = $request->auto_update;
            $rssFeed->show_read_more    = $request->show_read_more;
            $rssFeed->keep_date         = $request->keep_date;
            $rssFeed->status            = $request->status;
            $rssFeed->meta_keywords     = $request->meta_keywords;
            $rssFeed->meta_description  = $request->meta_description;
            $rssFeed->tags              = $request->tags;
            $rssFeed->scheduled_date    = Carbon::parse($request->scheduled_date);
            $rssFeed->layout            = $request->layout;

            $rssFeed->save();

            return redirect()->back()->with('success', __('successfully_updated'));
        } catch (\Exception $e) {
            return view('site.pages.500');
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function filter(Request $request)
    {
        $categories     = Category::all();
        $activeLang     = Language::where('status', 'active')->orderBy('name', 'ASC')->get();
        $feeds          = RssFeed::where('language', 'like', '%' . $request->language . '%')->where('name', 'like', '%' . $request->search_key . '%')->orderBy('id', 'desc')->with('category', 'subCategory')->paginate('15');

        return view('post::search_rss_feeds', compact('activeLang', 'categories', 'feeds'));
    }





    public function getImg($item, $namespaces)
    {
        // Method 1: Standard RSS enclosure tag
        if (isset($item->enclosure) && isset($item->enclosure['url'])) {
            $url = (string)$item->enclosure['url'];
            if ($this->isValidImageUrl($url)) {
                return $this->character_convert($url);
            }
        }

        // Method 2: Media RSS namespace (media:content, media:thumbnail)
        if (isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);

            // media:content
            if (isset($media->content)) {
                foreach ($media->content as $content) {
                    $attrs = $content->attributes();
                    if (isset($attrs->url)) {
                        $url = (string)$attrs->url;
                        if ($this->isValidImageUrl($url)) {
                            return $this->character_convert($url);
                        }
                    }
                }
            }

            // media:group->media:content
            if (isset($media->group->content)) {
                foreach ($media->group->content as $content) {
                    $attrs = $content->attributes();
                    if (isset($attrs->url)) {
                        $url = (string)$attrs->url;
                        if ($this->isValidImageUrl($url)) {
                            return $this->character_convert($url);
                        }
                    }
                }
            }

            // media:thumbnail
            if (isset($media->thumbnail)) {
                foreach ($media->thumbnail as $thumb) {
                    $attrs = $thumb->attributes();
                    if (isset($attrs->url)) {
                        $url = (string)$attrs->url;
                        if ($this->isValidImageUrl($url)) {
                            return $this->character_convert($url);
                        }
                    }
                }
            }
        }

        // Method 3: content:encoded namespace (WordPress full content)
        if (isset($namespaces['content'])) {
            $content = $item->children($namespaces['content']);
            if (isset($content->encoded)) {
                $html = (string)$content->encoded;
                $imageUrl = $this->extractImageFromHtml($html);
                if ($imageUrl) {
                    return $this->character_convert($imageUrl);
                }
            }
        }

        // Method 4: Description field (common in WordPress excerpt)
        if (isset($item->description)) {
            $html = (string)$item->description;
            $imageUrl = $this->extractImageFromHtml($html);
            if ($imageUrl) {
                return $this->character_convert($imageUrl);
            }
        }

        // Method 5: Custom fullimage tag (some feeds)
        if (isset($item->fullimage)) {
            $url = (string)$item->fullimage;
            if ($this->isValidImageUrl($url)) {
                return $this->character_convert($url);
            }
        }

        // Method 6: Fetch from source URL (fallback - slower)
        if (isset($item->link) && $this->shouldFetchFromSource()) {
            $articleUrl = (string)$item->link;
            $imageUrl = $this->fetchImageFromArticle($articleUrl);
            if ($imageUrl) {
                return $this->character_convert($imageUrl);
            }
        }

        return '';
    }

    /**
     * Extract image URL from HTML content
     */
    private function extractImageFromHtml($html)
    {
        if (empty($html)) {
            return null;
        }

        // Remove CDATA if present
        $html = preg_replace('/<!\[CDATA\[(.*?)\]\]>/s', '$1', $html);

        // Try to find img tag with src
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
            $url = $matches[1];
            if ($this->isValidImageUrl($url)) {
                return $url;
            }
        }

        // Try to find background image in style
        if (preg_match('/background-image:\s*url\(["\']?([^"\')\s]+)["\']?\)/i', $html, $matches)) {
            $url = $matches[1];
            if ($this->isValidImageUrl($url)) {
                return $url;
            }
        }

        return null;
    }

    /**
     * Fetch featured image from article source page
     */
    private function fetchImageFromArticle($url)
    {
        try {
            // Use cURL for better control and timeout
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                CURLOPT_SSL_VERIFYPEER => false, // For development only
            ]);

            $html = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || empty($html)) {
                return null;
            }

            // Priority 1: Open Graph image (most reliable)
            if (preg_match('/<meta[^>]+(?:property|name)=["\']og:image["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
                return $matches[1];
            }
            if (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+(?:property|name)=["\']og:image["\'][^>]*>/i', $html, $matches)) {
                return $matches[1];
            }

            // Priority 2: Twitter card image
            if (preg_match('/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
                return $matches[1];
            }
            if (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+name=["\']twitter:image["\'][^>]*>/i', $html, $matches)) {
                return $matches[1];
            }

            // Priority 3: WordPress featured image class
            if (preg_match('/<img[^>]+class=["\'][^"\']*wp-post-image[^"\']*["\'][^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
                return $matches[1];
            }
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]+class=["\'][^"\']*wp-post-image[^"\']*["\'][^>]*>/i', $html, $matches)) {
                return $matches[1];
            }

            // Priority 4: Article tag first image
            if (preg_match('/<article[^>]*>.*?<img[^>]+src=["\']([^"\']+)["\'][^>]*>/is', $html, $matches)) {
                return $matches[1];
            }

            // Priority 5: Any large image (width >= 400px)
            if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
                foreach ($matches[1] as $imgUrl) {
                    // Skip small icons, logos, etc.
                    if (!preg_match('/(icon|logo|avatar|thumb)/i', $imgUrl)) {
                        return $imgUrl;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch image from article: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Check if URL is a valid image URL
     */
    private function isValidImageUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        // Check if it's a valid URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check for common image extensions
        if (preg_match('/\.(jpg|jpeg|png|gif|webp|bmp|svg)(\?.*)?$/i', $url)) {
            return true;
        }

        // Check for image-related patterns in URL
        if (preg_match('/(image|img|photo|picture|media)/i', $url)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if we should fetch from source (to avoid too many requests)
     * You can customize this logic based on your needs
     */
    private function shouldFetchFromSource()
    {
        // Only fetch from source if we haven't found image through other methods
        // You might want to add more conditions here:
        // - Check if URL is from trusted domain
        // - Implement rate limiting
        // - Use queue for background processing
        return true;
    }

    /**
     * Get media type from item
     */

    /**
     * Updated manualImport method with better error handling
     */
    public function manualImport($id)
    {
        if (strtolower(\Config::get('app.demo_mode')) == 'yes') {
            return redirect()->back()->with('error', __('You are not allowed to add/modify in demo mode.'));
        }

        $feed = RssFeed::findOrFail($id);

        try {
            // Load RSS feed
            $feeds = simplexml_load_file($feed->feed_url, null, LIBXML_NOCDATA);

            $main_url = (string) $feeds->channel->image->url ?? NULL;

            if (empty($feeds)) {
                return redirect()->back()->with('error', __('Failed to load RSS feed.'));
            }

            // Get namespaces
            $namespaces = $feeds->getNamespaces(true);

            $i = 0;
            $imported = 0;
            $skipped = 0;

            foreach ($feeds->channel->item as $key => $item) {
                // Check post limit
                if ($feed->post_limit > 0 && $i >= $feed->post_limit) {
                    break;
                }

                // Check if post already exists
                $hasAlready = Post::where('title', (string)$item->title)
                    ->orWhere('slug', $this->make_slug((string)$item->title))
                    ->first();

                if (!empty($hasAlready)) {
                    $skipped++;
                    continue;
                }

                // Create new post
                $post = new Post();
                $post->title = (string)$item->title;
                $post->slug = $this->make_slug((string)$item->title);
                $post->content = (string)$item->description;

                // Set date
                if ($feed->keep_date && isset($item->pubDate)) {
                    $post->created_at = Carbon::parse((string)$item->pubDate);
                }

                // Set read more link
                if ($feed->show_read_more && isset($item->link)) {
                    $post->read_more_link = (string)$item->link;
                }

                // Set other fields
                $post->language = $feed->language;
                $post->category_id = $feed->category_id;
                $post->sub_category_id = $feed->sub_category_id;
                $post->layout = $feed->layout;

                // Set status
                if ($feed->status == 2) {
                    $post->status = 0;
                    $post->scheduled = 1;
                    $post->scheduled_date = $feed->scheduled_date;
                    $post->visibility = 1;
                } elseif ($feed->status == 0) {
                    $post->status = $feed->status;
                } else {
                    $post->status = $feed->status;
                    $post->visibility = 1;
                }

                $post->user_id = Sentinel::getUser()->id;
                $post->source_content = self::getDomainNameWithoutTLD($feed->feed_url);
                $post->tags = $feed->tags;
                $post->meta_keywords = $feed->meta_keywords;
                $post->meta_description = $feed->meta_description;

                // Get and process image
                $imageUrl = $this->getImg($item, $namespaces);
                if (!empty($imageUrl)) {
                    // Determine post type based on media
                    if (preg_match('/\.(mp4|3gp|webm|mov|avi)(\?.*)?$/i', $imageUrl)) {
                        $post->post_type = 'video';
                        $post->video_url_type = 'mp4_url';
                        $post->video_url = $imageUrl;
                    } else {
                        $post->post_type = 'article';
                        try {
                            $post->image_id = $this->imageUpload($imageUrl, $this->getType($item));
                        } catch (\Exception $e) {
                        }
                    }
                } else {
                    $post->post_type = 'article';
                    if (!empty($main_url)) {
                        $post->image_id = $this->imageUpload(self::cleanImageUrl($main_url), $this->getType($item));
                    }
                }

                $post->save();
                $imported++;
                $i++;
            }

            Cache::flush();

            $message = "Successfully imported {$imported} posts.";
            if ($skipped > 0) {
                $message .= " Skipped {$skipped} duplicate posts.";
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('sorry_invalid_rss_feed_url') . ': ' . $e->getMessage());
        }
    }

   public static function getDomainNameWithoutTLD($url) {
        // Parse the host from the URL
        $host = parse_url($url, PHP_URL_HOST);
    
        if (!$host) {
            return null; // Invalid URL
        }
    
        // Remove the TLD (like ".com")
        $name = preg_replace('/\.[^.]+$/', '', $host);
    
        return $name;
    }

    public static function cleanImageUrl($url)
    {
        return preg_replace('/\?.*/', '', $url);
    }


    public function downloadImage($url)
    {
        try {
            $cleanUrl = strtok($url, '?'); // Remove ?resize or other query params

            $imageData = @file_get_contents($cleanUrl);

            if (!$imageData) {
                return null;
            }

            // Detect file extension
            $ext = pathinfo($cleanUrl, PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])) {
                $ext = 'jpg'; // fallback
            }

            // Generate local filename
            $filename = time() . '-' . uniqid() . '.' . $ext;

            $path = public_path('uploads/rss/' . $filename);

            // Save raw file
            file_put_contents($path, $imageData);

            return 'uploads/rss/' . $filename;
        } catch (\Exception $e) {
            \Log::error("Image download failed: " . $e->getMessage());
            return null;
        }
    }


    public function getType($item = '')
    {
        return $this->character_convert(preg_replace("/img\//", "", @$item->enclosure['type']));
    }

    public function character_convert($str)
    {
        $str = trim($str);
        $str = str_replace("&amp;", "&", $str);
        $str = str_replace("&lt;", "<", $str);
        $str = str_replace("&gt;", ">", $str);
        $str = str_replace("&quot;", '"', $str);
        $str = str_replace("&apos;", "'", $str);
        return $str;
    }

    private function make_slug($string, $delimiter = '-')
    {

        $string = preg_replace("/[~`{}.'\"\!\@\#\$\%\^\&\*\(\)\_\=\+\/\?\>\<\,\[\]\:\;\|\\\]/", "", $string);

        $string = preg_replace("/[\/_|+ -]+/", $delimiter, $string);
        $result = mb_strtolower($string);

        if ($result):
            return $result;
        else:
            return $string;
        endif;
    }


    public function imageUpload($request, $type = '')
{
    try {
        $image = new galleryImage();
        $requestImage = $request;
        
        // Check if the input is a URL
        $isUrl = filter_var($requestImage, FILTER_VALIDATE_URL);
        
        // If it's a URL, download it to a temporary file first
        if ($isUrl) {
            $tempFile = tempnam(sys_get_temp_dir(), 'img_');
            
            // Initialize cURL
            $ch = curl_init($requestImage);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $imageData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200 || $imageData === false) {
                throw new \Exception("Unable to fetch image from URL: " . $requestImage);
            }
            
            file_put_contents($tempFile, $imageData);
            $requestImage = $tempFile;
            
            // Get file type from the downloaded file
            $imageInfo = getimagesize($requestImage);
            $mime = $imageInfo['mime'] ?? '';
            
            // Map MIME type to file extension
            $mimeToExt = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/bmp' => 'bmp',
            ];
            
            $fileType = $mimeToExt[$mime] ?? 'jpg';
        } else {
            $fileType = preg_replace("/.*\./", "", $requestImage);
        }

        if ($type != '') {
            $fileType = $type;
        }

        $originalImageName = date('YmdHis') . "_original_" . rand(1, 50) . '.webp';
        $ogImageName = date('YmdHis') . "_ogImage_" . rand(1, 50) . '.webp';
        $thumbnailImageName = date('YmdHis') . "_thumbnail_100x100_" . rand(1, 50) . '.webp';
        $bigImageName = date('YmdHis') . "_big_1080x1000_" . rand(1, 50) . '.' . 'webp';
        $bigImageNameTwo = date('YmdHis') . "_big_730x400_" . rand(1, 50) . '.' . 'webp';
        $mediumImageName = date('YmdHis') . "_medium_358x215_" . rand(1, 50) . '.' . 'webp';
        $mediumImageNameTwo = date('YmdHis') . "_medium_350x190_" . rand(1, 50) . '.' . 'webp';
        $mediumImageNameThree = date('YmdHis') . "_medium_255x175_" . rand(1, 50) . '.' . 'webp';
        $smallImageName = date('YmdHis') . "_small_123x83_" . rand(1, 50) . '.' . 'webp';

        if (strpos(php_sapi_name(), 'cli') !== false || settingHelper('default_storage') == 's3' || defined('LARAVEL_START_FROM_PUBLIC')) {
            $directory = 'images/';
        } else {
            $directory = 'public/images/';
        }

        $originalImageUrl = $directory . $originalImageName;
        $ogImageUrl = $directory . $ogImageName;
        $thumbnailImageUrl = $directory . $thumbnailImageName;
        $bigImageUrl = $directory . $bigImageName;
        $bigImageUrlTwo = $directory . $bigImageNameTwo;
        $mediumImageUrl = $directory . $mediumImageName;
        $mediumImageUrlTwo = $directory . $mediumImageNameTwo;
        $mediumImageUrlThree = $directory . $mediumImageNameThree;
        $smallImageUrl = $directory . $smallImageName;

        if (settingHelper('default_storage') == 's3') {
            // Use Image::make() directly - it handles all formats
            $imgOg = Image::make($requestImage)->fit(730, 400)->stream();
            $imgOriginal = Image::make($requestImage)->encode('webp', 80);
            $imgThumbnail = Image::make($requestImage)->fit(100, 100)->encode('webp', 80);
            $imgBig = Image::make($requestImage)->fit(1080, 1000)->encode('webp', 80);
            $imgBigTwo = Image::make($requestImage)->fit(730, 400)->encode('webp', 80);
            $imgMedium = Image::make($requestImage)->fit(358, 215)->encode('webp', 80);
            $imgMediumTwo = Image::make($requestImage)->fit(350, 190)->encode('webp', 80);
            $imgMediumThree = Image::make($requestImage)->fit(255, 175)->encode('webp', 80);
            $imgSmall = Image::make($requestImage)->fit(123, 83)->encode('webp', 80);

            try {
                Storage::disk('s3')->put($originalImageUrl, $imgOriginal);
                Storage::disk('s3')->put($ogImageUrl, $imgOg);
                Storage::disk('s3')->put($thumbnailImageUrl, $imgThumbnail);
                Storage::disk('s3')->put($bigImageUrl, $imgBig);
                Storage::disk('s3')->put($bigImageUrlTwo, $imgBigTwo);
                Storage::disk('s3')->put($mediumImageUrl, $imgMedium);
                Storage::disk('s3')->put($mediumImageUrlTwo, $imgMediumTwo);
                Storage::disk('s3')->put($mediumImageUrlThree, $imgMediumThree);
                Storage::disk('s3')->put($smallImageUrl, $imgSmall);
            } catch (\Exception $e) {
                $data['status'] = 'error';
                $data['message'] = $e->getMessage();
                return Response()->json($data);
            }
        } elseif (settingHelper('default_storage') == 'local') {
            // Use Image::make() directly - it handles all formats
            Image::make($requestImage)->encode('webp', 80)->save($originalImageUrl);
            Image::make($requestImage)->fit(730, 400)->save($ogImageUrl);
            Image::make($requestImage)->fit(100, 100)->encode('webp', 80)->save($thumbnailImageUrl);
            Image::make($requestImage)->fit(1080, 1000)->encode('webp', 80)->save($bigImageUrl);
            Image::make($requestImage)->fit(730, 400)->encode('webp', 80)->save($bigImageUrlTwo);
            Image::make($requestImage)->fit(358, 215)->encode('webp', 80)->save($mediumImageUrl);
            Image::make($requestImage)->fit(350, 190)->encode('webp', 80)->save($mediumImageUrlTwo);
            Image::make($requestImage)->fit(255, 175)->encode('webp', 80)->save($mediumImageUrlThree);
            Image::make($requestImage)->fit(123, 83)->encode('webp', 80)->save($smallImageUrl);
        }

        $image->original_image = str_replace("public/", "", $originalImageUrl);
        $image->og_image = str_replace("public/", "", $ogImageUrl);
        $image->thumbnail = str_replace("public/", "", $thumbnailImageUrl);
        $image->big_image = str_replace("public/", "", $bigImageUrl);
        $image->big_image_two = str_replace("public/", "", $bigImageUrlTwo);
        $image->medium_image = str_replace("public/", "", $mediumImageUrl);
        $image->medium_image_two = str_replace("public/", "", $mediumImageUrlTwo);
        $image->medium_image_three = str_replace("public/", "", $mediumImageUrlThree);
        $image->small_image = str_replace("public/", "", $smallImageUrl);

        $image->disk = settingHelper('default_storage');
        $image->save();
        $image = galleryImage::latest()->first();

        // Clean up temporary file if it was created
        if ($isUrl && isset($tempFile) && file_exists($tempFile)) {
            unlink($tempFile);
        }

        return $image->id;
    } catch (\Exception $e) {
        Log::error('Image upload error: ' . $e->getMessage());
        return null;
    }
}
    public function imageUpload_old($request, $type = '')
    {
        try {
            $image = new galleryImage();
            $requestImage = $request;
            $fileType = preg_replace("/.*\./", "", $requestImage);

            if ($type != '') {
                $fileType = $type;
            }

            $originalImageName = date('YmdHis') . "_original_" . rand(1, 50) . '.' . 'webp';
            $ogImageName = date('YmdHis') . "_ogImage_" . rand(1, 50) . '.' . $fileType;
            $thumbnailImageName = date('YmdHis') . "_thumbnail_100x100_" . rand(1, 50) . '.' . 'webp';
            $bigImageName = date('YmdHis') . "_big_1080x1000_" . rand(1, 50) . '.' . 'webp';
            $bigImageNameTwo = date('YmdHis') . "_big_730x400_" . rand(1, 50) . '.' . 'webp';
            $mediumImageName = date('YmdHis') . "_medium_358x215_" . rand(1, 50) . '.' . 'webp';
            $mediumImageNameTwo = date('YmdHis') . "_medium_350x190_" . rand(1, 50) . '.' . 'webp';
            $mediumImageNameThree = date('YmdHis') . "_medium_255x175_" . rand(1, 50) . '.' . 'webp';
            $smallImageName = date('YmdHis') . "_small_123x83_" . rand(1, 50) . '.' . 'webp';


            if (strpos(php_sapi_name(), 'cli') !== false || settingHelper('default_storage') == 's3' || defined('LARAVEL_START_FROM_PUBLIC')) :
                $directory = 'images/';
            else:
                $directory = 'public/images/';
            endif;

            $originalImageUrl = $directory . $originalImageName;
            $ogImageUrl = $directory . $ogImageName;
            $thumbnailImageUrl = $directory . $thumbnailImageName;
            $bigImageUrl = $directory . $bigImageName;
            $bigImageUrlTwo = $directory . $bigImageNameTwo;
            $mediumImageUrl = $directory . $mediumImageName;
            $mediumImageUrlTwo = $directory . $mediumImageNameTwo;
            $mediumImageUrlThree = $directory . $mediumImageNameThree;
            $smallImageUrl = $directory . $smallImageName;


            if (settingHelper('default_storage') == 's3'):

                //ogImage
                $imgOg = Image::make($requestImage)->fit(730, 400)->stream();

                //jpg. jpeg, JPEG, JPG compression
                if ($fileType == 'jpeg' or $fileType == 'jpg' or $fileType == 'JPEG' or $fileType == 'JPG'):
                    $imgOriginal = Image::make(imagecreatefromjpeg($requestImage))->encode('webp', 80);
                    $imgThumbnail = Image::make(imagecreatefromjpeg($requestImage))->fit(100, 100)->encode('webp', 80);
                    $imgBig = Image::make(imagecreatefromjpeg($requestImage))->fit(1080, 1000)->encode('webp', 80);
                    $imgBigTwo = Image::make(imagecreatefromjpeg($requestImage))->fit(730, 400)->encode('webp', 80);
                    $imgMedium = Image::make(imagecreatefromjpeg($requestImage))->fit(358, 215)->encode('webp', 80);
                    $imgMediumTwo = Image::make(imagecreatefromjpeg($requestImage))->fit(350, 190)->encode('webp', 80);
                    $imgMediumThree = Image::make(imagecreatefromjpeg($requestImage))->fit(255, 175)->encode('webp', 80);
                    $imgSmall = Image::make(imagecreatefromjpeg($requestImage))->fit(123, 83)->encode('webp', 80);

                //png compression
                elseif ($fileType == 'PNG' or $fileType == 'png'):

                    $imgOriginal     = Image::make($requestImage)->encode('webp', 80);
                    $imgThumbnail    = Image::make($requestImage)->fit(100, 100)->encode('webp', 80);
                    $imgBig          = Image::make($requestImage)->fit(1080, 1000)->encode('webp', 80);
                    $imgBigTwo       = Image::make($requestImage)->fit(730, 400)->encode('webp', 80);
                    $imgMedium       = Image::make($requestImage)->fit(358, 215)->encode('webp', 80);
                    $imgMediumTwo    = Image::make($requestImage)->fit(350, 190)->encode('webp', 80);
                    $imgMediumThree  = Image::make($requestImage)->fit(255, 175)->encode('webp', 80);
                    $imgSmall        = Image::make($requestImage)->fit(123, 83)->encode('webp', 80);


                endif;

                try {
                    Storage::disk('s3')->put($originalImageUrl, $imgOriginal);
                    Storage::disk('s3')->put($ogImageUrl, $imgOg);
                    Storage::disk('s3')->put($thumbnailImageUrl, $imgThumbnail);
                    Storage::disk('s3')->put($bigImageUrl, $imgBig);
                    Storage::disk('s3')->put($bigImageUrlTwo, $imgBigTwo);
                    Storage::disk('s3')->put($mediumImageUrl, $imgMedium);
                    Storage::disk('s3')->put($mediumImageUrlTwo, $imgMediumTwo);
                    Storage::disk('s3')->put($mediumImageUrlThree, $imgMediumThree);
                    Storage::disk('s3')->put($smallImageUrl, $imgSmall);
                } catch (S3 $e) {
                    $data['status'] = 'error';
                    $data['message'] = $e->getMessage();
                    return Response()->json($data);
                }
            elseif (settingHelper('default_storage') == 'local'):
                Image::make($requestImage)->fit(730, 400)->save($originalImageUrl);


                if ($fileType == 'jpeg' or $fileType == 'jpg' or $fileType == 'JPEG' or $fileType == 'JPG'):
                    Image::make(imagecreatefromjpeg($requestImage))->save($originalImageUrl, 80);

                    Image::make(imagecreatefromjpeg($requestImage))->fit(100, 100)->save($thumbnailImageUrl, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(1080, 1000)->save($bigImageUrl, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(730, 400)->save($bigImageUrlTwo, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(358, 215)->save($mediumImageUrl, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(350, 190)->save($mediumImageUrlTwo, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(255, 175)->save($mediumImageUrlThree, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(123, 83)->save($smallImageUrl, 80);

                elseif ($fileType == 'PNG' or $fileType == 'png'):
                    Image::make(imagecreatefrompng($requestImage))->save($originalImageUrl, 80);

                    Image::make(imagecreatefrompng($requestImage))->fit(100, 100)->save($thumbnailImageUrl, 80);
                    Image::make(imagecreatefrompng($requestImage))->fit(1080, 1000)->save($bigImageUrl, 80);
                    Image::make(imagecreatefrompng($requestImage))->fit(730, 400)->save($bigImageUrlTwo, 80);
                    Image::make(imagecreatefrompng($requestImage))->fit(358, 215)->save($mediumImageUrl, 80);
                    Image::make(imagecreatefrompng($requestImage))->fit(350, 190)->save($mediumImageUrlTwo, 80);
                    Image::make(imagecreatefrompng($requestImage))->fit(255, 175)->save($mediumImageUrlThree, 80);
                    Image::make(imagecreatefrompng($requestImage))->fit(123, 83)->save($smallImageUrl, 80);
                else:
                    Image::make(imagecreatefromjpeg($requestImage))->save($originalImageUrl, 80);

                    Image::make(imagecreatefromjpeg($requestImage))->fit(100, 100)->save($thumbnailImageUrl, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(1080, 1000)->save($bigImageUrl, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(730, 400)->save($bigImageUrlTwo, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(358, 215)->save($mediumImageUrl, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(350, 190)->save($mediumImageUrlTwo, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(255, 175)->save($mediumImageUrlThree, 80);
                    Image::make(imagecreatefromjpeg($requestImage))->fit(123, 83)->save($smallImageUrl, 80);
                endif;
            endif;

            $image->original_image = str_replace("public/", "", $originalImageUrl);
            $image->og_image = str_replace("public/", "", $ogImageUrl);
            $image->thumbnail = str_replace("public/", "", $thumbnailImageUrl);
            $image->big_image = str_replace("public/", "", $bigImageUrl);
            $image->big_image_two = str_replace("public/", "", $bigImageUrlTwo);
            $image->medium_image = str_replace("public/", "", $mediumImageUrl);
            $image->medium_image_two = str_replace("public/", "", $mediumImageUrlTwo);
            $image->medium_image_three = str_replace("public/", "", $mediumImageUrlThree);
            $image->small_image = str_replace("public/", "", $smallImageUrl);

            $image->disk = settingHelper('default_storage');
            $image->save();
            $image = galleryImage::latest()->first();

            return $image->id;
        } catch (\Exception $e) {
            dd($e->getMessage());
            Log::error($e->getMessage());
            return null;
        }
    }
}
