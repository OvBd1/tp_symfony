<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create Admin User
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setCreatedAt(new \DateTime());
        $manager->persist($admin);

        // Create Regular User
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $user->setCreatedAt(new \DateTime());
        $manager->persist($user);

        // Create Categories
        $categories = [];
        $categoryNames = ['Technology', 'Lifestyle', 'Programming', 'Web Development', 'Design'];
        
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription("Articles about $name");
            $manager->persist($category);
            $categories[] = $category;
        }

        // Create Posts
        $postTitles = [
            'Getting Started with Symfony',
            'Bootstrap 5 Best Practices',
            'Understanding MVC Architecture',
            'Database Design Principles',
            'Frontend vs Backend Development',
            'Security Best Practices in Web Development',
            'Introduction to Docker',
            'API Design Patterns',
            'Testing in Symfony',
            'Performance Optimization Tips'
        ];

        $postContents = [
            'Symfony is a powerful PHP framework that helps you build scalable web applications. In this article, we\'ll explore the basics of Symfony and how to get started with your first project.',
            'Bootstrap 5 is a popular CSS framework that makes responsive web design easier. Learn about the new features and improvements in Bootstrap 5.',
            'The MVC (Model-View-Controller) architecture is a fundamental pattern in web development. Let\'s dive deep into how it works and why it\'s important.',
            'Good database design is crucial for application performance. In this article, we\'ll discuss key principles and best practices for designing databases.',
            'Frontend and backend development are both crucial parts of web development. Let\'s explore the differences, challenges, and best practices for each.',
            'Security should be a top priority in any web application. Learn about common vulnerabilities and how to protect your application.',
            'Docker is a containerization platform that makes deployment easier. Get started with Docker and learn how to containerize your applications.',
            'A well-designed API is essential for modern web applications. Learn about REST principles, versioning, and other important API design patterns.',
            'Testing is crucial for maintaining code quality. Discover different testing approaches in Symfony and how to write effective tests.',
            'Performance optimization can significantly impact user experience. Learn about profiling, caching, and other techniques to improve performance.'
        ];

        for ($i = 0; $i < 10; $i++) {
            $post = new Post();
            $post->setTitle($postTitles[$i]);
            $post->setContent($postContents[$i]);
            $post->setAuthor($admin);
            $post->setCategory($categories[$i % count($categories)]);
            $post->setPublishedAt(new \DateTime('-' . $i . ' days'));
            $post->setPicture('https://via.placeholder.com/500x300?text=' . urlencode($postTitles[$i]));
            $manager->persist($post);

            // Add Comments
            for ($j = 0; $j < 2; $j++) {
                $comment = new Comment();
                $comment->setContent('Great article! I really enjoyed reading this and learned a lot.');
                $comment->setAuthor($j % 2 == 0 ? $admin : $user);
                $comment->setPost($post);
                $comment->setCreatedAt(new \DateTime('-' . ($i + $j) . ' hours'));
                $comment->setStatus('approved');
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
