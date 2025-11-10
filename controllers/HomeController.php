<?php
class HomeController extends Controller {
    
    public function index() {
        $movieModel = new MovieModel();
        
        $hotMovies = $movieModel->getHotMovies(6);
        $latestMovies = $movieModel->getAll(12);
        
        $this->view('home/index', [
            'hotMovies' => $hotMovies,
            'latestMovies' => $latestMovies,
            'user' => $this->getCurrentUser()
        ]);
    }
}
?>

