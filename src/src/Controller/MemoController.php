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
    public function index(Request $request): Response
    {
        $priority = $request->query->get('priority');

        $memos = null;
        if ($priority === null || $priority === '' || $priority === 'all') {
            $memos = $this->memoRepository->findAll();
        } else {
            $normalized = match ($priority) {
                'low', '0' => 0,
                'medium', '1' => 1,
                'high', '2' => 2,
                default => null,
            };

            if ($normalized === null) {
                $memos = $this->memoRepository->findAll();
            } else {
                $memos = $this->memoRepository->findByPriority($normalized);
            }
        }

        $groupedMemos = [
            Memo::STATUS_NOT_STARTED => [],
            Memo::STATUS_IN_PROGRESS => [],
            Memo::STATUS_DONE => [],
        ];

        foreach ($memos as $memo) {
            $status = $memo->getStatus() ?? Memo::STATUS_NOT_STARTED;
            $groupedMemos[$status][] = $memo;
        }

        return $this->render('memo/index.html.twig', [
            'memos' => $memos,
            'groupedMemos' => $groupedMemos,
            'selected_priority' => $priority,
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

    #[Route(path: '/memo/{id}/status', name: 'memo_status_update', methods: ['POST'])]
    public function updateStatus(Request $request, Memo $memo): Response
    {
        $data = json_decode($request->getContent(), true);
        $status = isset($data['status']) ? (int) $data['status'] : null;

        $validStatuses = [Memo::STATUS_NOT_STARTED, Memo::STATUS_IN_PROGRESS, Memo::STATUS_DONE];
        if (!in_array($status, $validStatuses, true)) {
            return $this->json(['error' => '無効なステータスです'], Response::HTTP_BAD_REQUEST);
        }

        $memo->setStatus($status);
        $this->memoRepository->saveMemo($memo);

        return $this->json(['status' => $memo->getStatus()]);
    }
}
