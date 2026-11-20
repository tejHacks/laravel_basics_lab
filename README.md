# Laravel Basics Lab

An interactive website that teaches **Laravel 13 from beginner to intermediate**: 30 lessons with code, quizzes and progress tracking, plus **live demos** whose source you can read.

## Run it
```bash
cd ~/Developer/laravel-basics-lab
php artisan migrate:fresh --seed   # builds the SQLite database with sample data
php artisan serve                  # open http://127.0.0.1:8000
```
Demo login (Auth demo): `learner@example.com` / `password`

## Where things are
- Lessons: `resources/lessons/*.md` (+ `curriculum.json` for the order)
- Lesson engine: `app/Lessons/`, `app/Http/Controllers/CourseController.php`
- Demos: `app/Http/Controllers/Demo/*`, `app/Models/Task.php`, `resources/views/demo/*`
- Tests: `php artisan test`
