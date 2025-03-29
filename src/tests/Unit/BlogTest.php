<?php

namespace YourVendor\BlogSystem\Tests\Feature;

use YourVendor\BlogSystem\Models\Blog;
use YourVendor\BlogSystem\Models\Category;
use YourVendor\BlogSystem\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_can_be_created_with_image()
    {
        Storage::fake('public');

        $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
        $image = UploadedFile::fake()->image('cover.jpg');

        $response = $this->postJson('/api/blogs', [
            'title' => 'Test Blog',
            'content' => 'This is a test blog.',
            'category_id' => $category->id,
            'cover_image' => $image,
            'status' => true,
            'meta_title' => 'Test Meta Title',
            'meta_description' => 'Test Meta Description',
            'meta_keywords' => 'test,blog,meta',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('blogs', ['title' => 'Test Blog']);
    }

    public function test_blog_can_be_updated_with_new_image()
    {
        Storage::fake('public');

        $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
        $blog = Blog::create([
            'title' => 'Old Blog',
            'slug' => 'old-blog',
            'content' => 'Old content',
            'category_id' => $category->id,
            'status' => true
        ]);

        $newImage = UploadedFile::fake()->image('new_cover.jpg');

        $response = $this->putJson("/api/blogs/{$blog->id}", [
            'title' => 'Updated Blog',
            'content' => 'Updated content',
            'cover_image' => $newImage,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('blogs', ['title' => 'Updated Blog']);
    }
}
