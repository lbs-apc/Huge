<?php

class GalleryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::checkAuthentication();
    }

    public function index()
    {
        $images = GalleryModel::getAllImages();
        $this->View->render('gallery/showGallery', array(
            'images' => $images
        ));
    }

    public function upload()
    {
        GalleryModel::uploadImage();
        Redirect::to('gallery/index');
    }

    public function delete($id)
    {
        GalleryModel::deleteImage($id);
        Redirect::to('gallery/index');
    }

    public function toggle($id)
    {
        GalleryModel::toggleShared($id);
        Redirect::to('gallery/index');
    }

    public function show($id)
    {
        GalleryModel::displayImage($id);
    }
}
