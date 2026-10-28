<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use Core\Controller;

class CategoryController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $categories = Category::getForUser($userId);

        $this->render('categories.index', [
            'title'      => 'Categories - Dischedule',
            'categories' => $categories
        ], 'main');
    }

    public function store(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $input = $this->request->all();

        $this->validate($input, [
            'name'  => 'required|max:100',
            'color' => 'required'
        ]);

        $isGlobal = ($this->isAdmin() && !empty($input['is_global'])) ? 1 : 0;

        $catId = Category::create([
            'user_id'    => $isGlobal ? null : $userId,
            'name'       => trim((string)$input['name']),
            'color'      => trim((string)$input['color']),
            'icon'       => !empty($input['icon']) ? trim((string)$input['icon']) : 'fa-solid fa-calendar',
            'is_global'  => $isGlobal,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        ActivityLog::log($userId, 'category_created', "Created category '{$input['name']}'.");

        if ($this->request->isAjax()) {
            $this->json([
                'success'  => true,
                'message'  => 'Category created!',
                'category' => Category::find($catId)
            ]);
            return;
        }

        $this->flash('success', 'Category created successfully.');
        $this->redirect('/categories');
    }

    public function update(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $category = Category::find($id);

        if (!$category || ((int)$category['user_id'] !== $userId && !$this->isAdmin())) {
            $this->flash('error', 'Unauthorized or category not found.');
            $this->redirect('/categories');
            return;
        }

        $input = $this->request->all();
        $this->validate($input, [
            'name'  => 'required|max:100',
            'color' => 'required'
        ]);

        Category::update($id, [
            'name'  => trim((string)$input['name']),
            'color' => trim((string)$input['color']),
            'icon'  => !empty($input['icon']) ? trim((string)$input['icon']) : 'fa-solid fa-calendar'
        ]);

        ActivityLog::log($userId, 'category_updated', "Updated category '{$input['name']}'.");

        if ($this->request->isAjax()) {
            $this->json(['success' => true, 'message' => 'Category updated.']);
            return;
        }

        $this->flash('success', 'Category updated successfully.');
        $this->redirect('/categories');
    }

    public function destroy(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $category = Category::find($id);

        if (!$category || ((int)$category['user_id'] !== $userId && !$this->isAdmin())) {
            $this->flash('error', 'Unauthorized or category not found.');
            $this->redirect('/categories');
            return;
        }

        Category::delete($id);
        ActivityLog::log($userId, 'category_deleted', "Deleted category #{$id}.");

        if ($this->request->isAjax()) {
            $this->json(['success' => true, 'message' => 'Category removed.']);
            return;
        }

        $this->flash('success', 'Category removed.');
        $this->redirect('/categories');
    }
}
