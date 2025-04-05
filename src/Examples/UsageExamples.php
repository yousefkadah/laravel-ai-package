<?php

namespace App\Examples;

use App\Integration\LaravelAI;

/**
 * Example usage of the Laravel AI model.
 */
class UsageExamples
{
    /**
     * Example of classifying a single code sample.
     *
     * @return void
     */
    public function classifySingleSample()
    {
        // Create a new instance of LaravelAI
        $laravelAI = new LaravelAI();
        
        // Load the trained model
        $laravelAI->loadModel(__DIR__ . '/../../data/models/laravel_classifier.model');
        
        // Sample PHP code to classify
        $code = <<<'CODE'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'price', 'description'];
    
    protected $casts = [
        'price' => 'float',
        'active' => 'boolean',
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
CODE;
        
        // Classify the code
        $classification = $laravelAI->classifyCode($code);
        
        echo "Code classification: " . $classification . PHP_EOL;
    }
    
    /**
     * Example of classifying multiple code samples.
     *
     * @return void
     */
    public function classifyBatchSamples()
    {
        // Create a new instance of LaravelAI
        $laravelAI = new LaravelAI();
        
        // Load the trained model
        $laravelAI->loadModel(__DIR__ . '/../../data/models/laravel_classifier.model');
        
        // Sample PHP code samples to classify
        $samples = [
            // Model sample
            <<<'CODE'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    
    protected $hidden = ['password', 'remember_token'];
}
CODE,
            // Controller sample
            <<<'CODE'
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }
    
    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }
}
CODE,
            // Service sample
            <<<'CODE'
<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
CODE
        ];
        
        // Classify the code samples
        $classifications = $laravelAI->classifyBatch($samples);
        
        foreach ($classifications as $index => $classification) {
            echo "Sample " . ($index + 1) . " classification: " . $classification . PHP_EOL;
        }
    }
    
    /**
     * Example of using the Laravel AI model in a Laravel application.
     *
     * @return string
     */
    public function laravelApplicationExample()
    {
        // In a Laravel application, you would use the facade:
        // use LaravelAI;
        
        $code = <<<'CODE'
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }
}
CODE;
        
        // This would be the actual code in a Laravel application:
        // $classification = LaravelAI::classifyCode($code);
        
        // For demonstration purposes:
        $laravelAI = new LaravelAI();
        $laravelAI->loadModel(__DIR__ . '/../../data/models/laravel_classifier.model');
        $classification = $laravelAI->classifyCode($code);
        
        return "In a Laravel application, this would be classified as: " . $classification;
    }
}
