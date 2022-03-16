<?php

namespace App\Http\Controllers;

use App\Api\Person;
use App\Http\Requests\PersonUpdateRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Inovim\ApiConsumer\Api\Models\Poi;
use Inovim\ApiConsumer\Models\Data;

class PersonController extends Controller
{
    private $passwordVerificationProjects = ['miwa', 'inbw'];

    public function index($project)
    {
        return [
            'person' => $this->fetchCurrentPerson(),
            'poi' => $this->fetchCurrentPoi(),
        ];
    }

    /**
     * @param $project
     * @param $person
     * @param PersonUpdateRequest $request
     */
    public function update($project, PersonUpdateRequest $request, $person)
    {
        // @TODO send mail when invoice type is domicile?
        (app(Person::class))->patch($person, [
            'data' => [
                'id' => $request->get('id'),
                'type' => $request->get('type') ?? 'persons',
                'attributes' => $request->except(['id', 'type']),
            ],
        ]);
    }

    public function fetchCurrentPerson()
    {
        $person = collect((app(Person::class))
            ->find(auth()->user()->person_id))
            ->map(function (Data $person) {
                $properties = $person->attributes['properties'];
                $properties = $properties ?: [];

                if (!isset($properties['invoice_preferences'])) {
                    $properties['invoice_preferences'] = (object) [];
                }

                if (!isset($properties['privacy_check'])) {
                    $properties['privacy_check'] = false;
                }

                $personInfo = [
                    'id' => $person->id,
                    'first_name' => $person->attributes['first_name'],
                    'last_name' => $person->attributes['last_name'],
                    'salutation' => $person->attributes['salutation'],
                    'phone' => $person->attributes['phone'],
                    'mobile' => $person->attributes['mobile'],
                    'fax' => $person->attributes['fax'],
                    'email' => $person->attributes['email'],
                    'company_name' => $person->attributes['company_name'],
                    'vat' => $person->attributes['vat'],
                    'gender' => $person->attributes['gender'],
                    'bic' => $person->attributes['bic'],
                    'date_of_birth' => $person->attributes['date_of_birth'],
                    'pincode' => auth()->user()->pincode,
                    'properties' => $properties,
                    'deadline_at' => auth()->user()->deadline_at,
                    'completed_at' => auth()->user()->completed_at,
                    'password_updated_at' => auth()->user()->password_updated_at,
                    'deadline_passed' => $this->isDeadlinePassed(),
                    'impersonating' => Session::get('impersonate') ?? false,
                ];

                if (in_array(auth()->user()->project, $this->passwordVerificationProjects)) {
                    $personInfo['hasValidPassword'] = $this->validatePassword();
                }

                return $personInfo;
            })->first();

        return $person;
    }

    public function fetchCurrentPoi()
    {
        $poi = app(Poi::class)
            ->find(auth()->user()->poi_id)
            ->map(function (Data $poi) {
                return [
                    'id' => $poi->id,
                    'street' => $poi->attributes['street'] ?? null,
                    'city' => $poi->attributes['city'] ?? null,
                    'country' => $poi->attributes['country'] ?? null,
                    'number' => $poi->attributes['number'] ?? null,
                    'letter' => $poi->attributes['letter'] ?? null,
                    'addition' => $poi->attributes['addition'] ?? null,
                    'zipcode' => $poi->attributes['zipcode'] ?? null,
                    'position' => $poi->attributes['position'] ?? null,
                ];
            })->first();

        return $poi;
    }

    public function validatePassword()
    {
        if (auth()->user()->password && !Hash::needsRehash(auth()->user()->password)) {
            return !password_verify(auth()->user()->pincode, auth()->user()->password) ? 'valid' : 'invalid';
        }

        return 'invalid';
    }

    /**
     * Check if a user's deadline to complete the order is passed
     *
     * @return bool
     */
    protected function isDeadlinePassed()
    {
        if (!auth()->user()->deadline_at) {
            return false;
        }

        return auth()->user()->deadline_at <= now();
    }
}
