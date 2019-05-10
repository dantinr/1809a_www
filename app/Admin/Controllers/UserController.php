<?php

namespace App\Admin\Controllers;

use App\Model\UserModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserModel);

        $grid->uid('Uid');
        $grid->nickname('Nickname');
        $grid->email('Email');
        $grid->age('Age');
        $grid->sex('Sex');
        $grid->add_time('Add time')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });
        $grid->headimg('头像')->image();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(UserModel::findOrFail($id));

        $show->uid('Uid');
        $show->nickname('Nickname');
        $show->email('Email');
        $show->age('Age');
        $show->sex('Sex');
        $show->add_time('Add time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserModel);

        $form->number('uid', 'Uid');
        $form->text('nickname', 'Nickname');
        $form->email('email', 'Email');
        $form->switch('age', 'Age');
        $form->switch('sex', 'Sex');
        $form->number('add_time', 'Add time');
        $form->image('headimg', '头像');

        return $form;
    }
}
