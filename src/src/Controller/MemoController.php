<?php

namespace App\Controller;

use App\Entity\Memo;
use App\Form\MemoFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MemoRepository;
use Symfony\Component\HttpFoundation\Request;

final class MemoController extends AbstractController
{
    public function __construct(
        private readonly MemoRepository $memoRepository
    ) {}

    #[Route('/', name: 'memo_index')]
    public function index(): Response
    {
        $memos = $this->memoRepository->findAll();

        return $this->render('memo/index.html.twig', [
            'memos' => $memos,
        ]);
    }

    #[Route('/create', name: 'memo_create')]
    public function create(Request $request): Response
    {
        $memo = new Memo();

        $form = $this->createForm(MemoFormType::class, $memo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->memoRepository->saveMemo($memo);

            return $this->redirectToRoute('memo_index');
        }

        return $this->render('memo/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // 勉強になったのでメモ
    // Symfony\Bundle\FrameworkBundle\Controller\AbstractController のコントローラでは、ルートのパラメータとタイプヒントから自動でエンティティを取得します。
    // ルート定義: #[Route(path: '/memo/{id}/edit', ...)]
    // メソッド引数: Memo $memo
    // Symfony は id を見て自動で MemoRepository を使って find($id) する
    #[Route(path: '/memo/{id}/edit', name: 'memo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Memo $memo): Response
    {
        $form = $this->createForm(MemoFormType::class, $memo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->memoRepository->saveMemo($memo);
            return $this->redirectToRoute('memo_index');
        }

        return $this->render('memo/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/memo/{id}/delete', name: 'memo_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Memo $memo): Response
    {
        if ($this->isCsrfTokenValid('delete' . $memo->getId(), $request->request->get('_token'))) {
            $this->memoRepository->deleteMemo($memo);
            return $this->redirectToRoute('memo_index');
        }

        return $this->render('memo/delete.html.twig', [
            'memo' => $memo,
        ]);
    }
}
