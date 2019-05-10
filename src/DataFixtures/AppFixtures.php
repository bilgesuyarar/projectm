<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\This;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $blog = new Blog();
            $blog->setTitle('blog' . $i);
            $blog->setContent('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
            for($j = 0; $j <5; $j++){
                $comment = new Comment();
                $comment->setEmail('comment@c.com');
                $comment->setContent('hdsjahkjdshjkdhfjhdskjfhkdhksjdhfsffd');
                $comment->setFullname('user' . $i);
                $manager->persist($comment);
                $blog->addComment($comment);
            }
            $manager->persist($blog);
        }
        $manager->flush();
    }
}
