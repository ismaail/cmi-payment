<?php

declare(strict_types=1);

namespace Combindma\Cmi\Traits;

use Combindma\Cmi\Cmi;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

/**
 * @phpcs:disable Generic.Files.LineLength.TooLong
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
trait CmiGateway
{
    public function requestPayment(Cmi $cmiClient, array $params = []): View|RedirectResponse
    {
        try {
            $cmiClient->guardAgainstInvalidRequest();
            $payData = $cmiClient->getCmiData($params);
            $hash = $cmiClient->getHash($params);
        } catch (\Exception $e) {
            Log::error($e);

            return redirect($cmiClient->getShopUrl())
                ->withErrors([
                    'payment' => __('Une erreur est survenue au niveau de la requête, veuillez réessayer ultérieurement.'),
                ]);
        }

        return view('cmi::request-payment', compact('cmiClient', 'payData', 'hash'));
    }

    public function callback(Request $request): View
    {
        $postData = $request->all();

        if ($postData) {
            $cmiClient = new Cmi();

            if ($_POST['ProcReturnCode'] === '00' && $cmiClient->validateHash($postData, $postData['HASH'])) {
                $response = 'ACTION=POSTAUTH';
            } else {
                $response = 'FAILURE';
            }
        } else {
            $response = 'No Data POST';
        }

        return view('cmi::callback', compact('response'));
    }

    public function okUrl(Request $request)
    {
        /*
         * Dans le cas d'une transaction approuvée, le client sera redirigé ici (paramètre envoyé par le site
         * marchand dans la demande de paiement). Toutes les données reçues dans la demande de paiement
         * du site marchand, ainsi que toutes les données de la transaction traitée seront envoyées par la plateforme
         * CMI vers ce okUrl.
         * */

        //C'est ici où vous pouvez gérer l'état de la commande
    }

    public function failUrl(Request $request)
    {
        /*
         * Dans le cas d'une transaction échouée, le client sera redirigé ici (paramètre envoyé par le site
         * marchand dans la demande de paiement). Toutes les données reçues dans la demande de paiement
         * du site marchand, ainsi que toutes les données de la transaction traitée seront envoyées par la plateforme CMI vers failUrl.
         * */

        //Par défaut nous redirigeons l'utilisateur vers la page shopUrl avec un message d'erreur
        $cmiClient = new Cmi();

        return redirect($cmiClient->getShopUrl())
            ->withErrors([
                'payment' => __('Paiement échoué, une erreur est survenue lors de la transaction, veuillez réessayer ultérieurement.'),
            ]);
    }
}
