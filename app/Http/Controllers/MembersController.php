<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\MemberCreateRequest;
use App\Http\Requests\MemberUpdateRequest;
use App\Repositories\MemberRepository;
use App\Validators\MemberValidator;
use Auth;
/**
 * Class MembersController.
 *
 * @package namespace App\Http\Controllers;
 */
class MembersController extends Controller
{
    /**
     * @var MemberRepository
     */
    protected $repository;

    /**
     * @var MemberValidator
     */
    protected $validator;

    /**
     * MembersController constructor.
     *
     * @param MemberRepository $repository
     * @param MemberValidator $validator
     */
    public function __construct(MemberRepository $repository, MemberValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    public function me()
    {
        $me = Auth::guard('member')->user();
        $this->repository->setPresenter('App\Presenters\MemberPresenter');

        $member = $this->repository->find($me->id);
        return response()->json(
            $member,
            200
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $members = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $members,
            ]);
        }

        return view('members.index', compact('members'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  MemberCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(MemberCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $member = $this->repository->create($request->all());

            $response = [
                'message' => 'Member created.',
                'data'    => $member->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $member = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $member,
            ]);
        }

        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $member = $this->repository->find($id);

        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  MemberUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(MemberUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $member = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Member updated.',
                'data'    => $member->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'Member deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Member deleted.');
    }
}
