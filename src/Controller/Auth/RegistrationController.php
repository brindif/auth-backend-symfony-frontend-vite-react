<?php

namespace App\Controller\Auth;

use App\Entity\Auth\User;
use App\Security\Auth\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\Auth\RegisterInput;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/register', name: 'api_register', methods: ['POST'])]
class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    public function __invoke(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): Response
    {
        try{
            $data = $request->toArray();
        } catch (\JsonException $e) {
            return $this->json(['success' => false, 'message' => 'register.error.request'], Response::HTTP_BAD_REQUEST);
        }

        $dto = new RegisterInput();
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;

        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            foreach ($violations as $v) {
                return $this->json(['success' => false, 'message' => $v->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword($userPasswordHasher->hashPassword($user, $dto->password));
        $user->setIsVerified(false);

        $violations = $validator->validate($user);
        if (count($violations) > 0) {
            foreach ($violations as $v) {
                return $this->json(['success' => false, 'message' => $v->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $em->persist($user);
        $em->flush();

        // generate a signed url and email it to the user
        try {
            $this->emailVerifier->sendEmailConfirmation('api_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address(
                        $this->getParameter('mailer.email'),
                        $this->getParameter('mailer.sender')
                    ))
                    ->to((string) $user->getEmail())
                    ->subject($this->getParameter('mailer.subject'))
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
        } catch (\JsonException $e) {
            return $this->json(['success' => false, 'message' => 'register.error.send.confirmation'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['success' => true], Response::HTTP_CREATED);
    }
}
