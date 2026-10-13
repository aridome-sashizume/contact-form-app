<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource; // APIレスポンス整形用リソース
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * お問い合わせ一覧を取得（検索 + ページネーション）
     * GET /api/v1/contacts
     */
    public function index(IndexContactRequest $request)
    {
        // バリデーション済みの検索条件を取得
        $validated = $request->validated();

        // N+1問題回避のため、tags・category を事前ロード
        $query = Contact::query()->with(['tags', 'category']);

        // 名前 or メールアドレス（部分一致 / 完全一致）
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                // 名前（first_name + last_name の部分一致）
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                 // メール（完全一致・部分一致）
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // 性別（完全一致）
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // カテゴリ（完全一致）
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 日付（完全一致）
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // ページネーションを設定（デフォルト20）
        $perPage = $validated['per_page'] ?? 20;
        $contacts = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // ContactResource で整形し、meta情報を追加
        return ContactResource::collection($contacts)
            ->additional([
                'meta' => [
                    'current_page' => $contacts->currentPage(),
                    'last_page' => $contacts->lastPage(),
                    'per_page' => $contacts->perPage(),
                    'total' => $contacts->total(),
                ],
            ]);
    }

    /**
     * お問い合わせ詳細を取得
     * GET /api/v1/contacts/{contact}
     */
    public function show(Contact $contact)
    {
        // category・tagsをネスト化するためにロード
        $contact->load(['category', 'tags']);

        // ContactResource で整形して返却
        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * お問い合わせを新規作成
     * POST /api/v1/contacts
     */
    public function store(StoreContactRequest $request)
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();

        // バリデーション済みデータからお問い合わせ本体を作成
        $contact = Contact::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'gender' => $validated['gender'],
            'email' => $validated['email'],
            'tel' => $validated['tel'],
            'address' => $validated['address'],
            'building' => $validated['building'] ?? null,
            'category_id' => $validated['category_id'],
            'detail' => $validated['detail'],
        ]);

        // タグの紐づけ（任意項目）
        if (! empty($validated['tag_ids'])) {
            $contact->tags()->attach($validated['tag_ids']);
        }

        // category・tagsをロード
        $contact->load(['category', 'tags']);

        // contactをContactResourceでラップしたデータを返却
        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * お問い合わせを更新
     * PUT /api/v1/contacts/{contact}
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();

        // バリデーション済みデータ（必須項目）からお問い合わせを更新
        $contact->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'gender' => $validated['gender'],
            'email' => $validated['email'],
            'tel' => $validated['tel'],
            'address' => $validated['address'],
            'building' => $validated['building'] ?? null,
            'category_id' => $validated['category_id'],
            'detail' => $validated['detail'],
        ]);

        // タグの紐づけ（任意項目）
        if (! empty($validated['tag_ids'])) {
            $contact->tags()->sync($validated['tag_ids']);
        } else {
            // tag_ids が空の場合はタグを全解除
            $contact->tags()->sync([]);
        }

        // category・tagsをロード
        $contact->load(['category', 'tags']);

        // contactをContactResourceでラップしたデータを返却
        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * お問い合わせを削除
     * DELETE /api/v1/contacts/{contact}
     */
    public function destroy(Contact $contact)
    {
        // お問い合わせを削除
        $contact->delete();

        // 204 No Content を返却
        return response()->json(null, 204);
    }
}
