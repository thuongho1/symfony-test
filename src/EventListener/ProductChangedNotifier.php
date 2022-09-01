<?php

namespace App\EventListener;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


class ProductChangedNotifier implements EventSubscriberInterface
{
    protected MailerInterface $mailer;

    protected SerializerInterface $serializer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        $this->serializer = new Serializer([$normalizer], [$encoder]);
    }
    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {

        $product = $args->getObject();
        if (!$product instanceof Product) {
            return;
        }
        $this->sendNotifyEmail($product, 'updated');
    }

    public function postPersist(LifecycleEventArgs $args): void
    {

        $product = $args->getObject();
        if (!$product instanceof Product) {
            return;
        }
        $product->toArray();
        $this->sendNotifyEmail($product, 'created');
    }

    private function sendNotifyEmail($product, $action): void
    {

        $product_array = $this->serializer->normalize($product, null, [AbstractNormalizer::IGNORED_ATTRIBUTES => ['categories']]);

        $categories = $product->getCategories()->toArray();

        foreach ($categories as $category) {
            $product_array['categories'][] = $category->getTitle();
        }

        $email = (new TemplatedEmail())
            ->from('fabien@example.com')
            ->to(new Address('ryan@example.com'))
            ->subject("The product has been $action!")
            ->htmlTemplate('emails/product.html.twig')
            ->context([
                'product' => $product_array,
                'action' => $action
            ]);
        $this->mailer->send($email);

    }
}