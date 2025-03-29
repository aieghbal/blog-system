<?php

namespace YourVendor\BlogSystem\Controllers;

use YourVendor\BlogSystem\Models\Blog;
use YourVendor\BlogSystem\Models\Category;
use YourVendor\BlogSystem\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Blogs",
 *     description="مدیریت بلاگ‌ها"
 * )
 */
class BlogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/blogs",
     *     summary="دریافت لیست بلاگ‌ها",
     *     tags={"Blogs"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست بلاگ‌ها",
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Blog::with(['category', 'tags'])->paginate(config('blog.pagination', 10)));
    }

    /**
     * @OA\Post(
     *     path="/api/blogs",
     *     summary="ایجاد بلاگ جدید",
     *     tags={"Blogs"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"title", "content", "category_id"},
     *             @OA\Property(property="title", type="string", example="عنوان بلاگ"),
     *             @OA\Property(property="content", type="string", example="متن بلاگ"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="integer"), example={1,2}),
     *             @OA\Property(property="cover_image", type="string", format="binary"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="بلاگ ایجاد شد"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required',
            'category_id' => 'required|exists:categories,id',
            'tags'        => 'array',
            'tags.*'      => 'exists:tags,id',
            'cover_image' => 'nullable|image|max:2048',
            'status'      => 'boolean',
        ]);

        $blog = new Blog($request->all());
        $blog->slug = Str::slug($request->title);
        $blog->save();

        if ($request->hasFile('cover_image'))
        {
            $blog->addMedia($request->file('cover_image'))->toMediaCollection('cover');
        }

        $blog->tags()->sync($request->tags ?? []);

        return response()->json($blog->load('category', 'tags'), 201);
    }


    public function show(Blog $blog)
    {
        return response()->json($blog->load(['category', 'tags']));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'content'     => 'sometimes|required',
            'category_id' => 'sometimes|required|exists:categories,id',
            'tags'        => 'array',
            'tags.*'      => 'exists:tags,id',
            'cover_image' => 'nullable|image|max:2048',
            'status'      => 'boolean',
        ]);

        if ($request->has('title'))
        {
            $blog->slug = Str::slug($request->title);
        }

        $blog->update($request->all());

        if ($request->hasFile('cover_image'))
        {
            $blog->clearMediaCollection('cover');
            $blog->addMedia($request->file('cover_image'))->toMediaCollection('cover');
        }

        $blog->tags()->sync($request->tags ?? []);

        return response()->json($blog->load('category', 'tags'));
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully.']);
    }
}
