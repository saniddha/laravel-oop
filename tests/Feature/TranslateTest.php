<?php

namespace Tests\Feature;

use App\Http\Controllers\TranslationController;
use App\Http\Requests\TranslationRequest;
use App\Services\impl\GoogleTranslatorService;
use App\Services\TranslationService;
use Google\Cloud\Translate\V3\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\Mock;
use stdClass;
use Tests\TestCase;
use function PHPUnit\Framework\assertJson;

/**
 * Class TranslateTest
 * @package Tests\Feature
 */
class TranslateTest extends TestCase
{

    /**
     *
     * @return void
     */


    public function testTranslationCorrectResponse()
    {

        $response = $this->post('/api/translate',array("language"=>"fr","expression"=>"help others"));

        $response->assertStatus(200);
        $response->assertJson(array("translatedText"=>"aider les autres"));
    }
//

    /**
     *@return void
     */
    public function testTranslationInValidLanguageResponse()
    {

        $response = $this->post('/api/translate',array("language"=>"frq","expression"=>"help others"));

        $response->assertStatus(200);
        $response->assertJson(array("code"=>"400", "error"=> "Invalid Value"));
    }


    /**
     *@return void
     */
    public function testGoogleTranslateMethodSuccess()
    {

        $translationController = new GoogleTranslatorService();

        $response = $translationController->translate(array("language"=>"fr","expression"=>"help others"));
        $this->assertIsObject($response);
        $this->assertEquals($response->translatedText, "aider les autres");
    }

    /**
     *@return void
     */
    public function testGoogleTranslateMethodFail()
    {

        $translationController = new GoogleTranslatorService();

        $response = $translationController->translate(array("language"=>"frds","expression"=>"help others"));
        $this->assertIsObject($response);
        $this->assertEquals($response->code, 400);
        $this->assertEquals($response->error, "Invalid Value");
    }

    /**
     *@return void
     */
    public function test_controller_index_method_translate_text_success()
    {

        $returnObj = new StdClass;
        $returnObj->translatedText = "aider les autres";

        $translator=$this->createMock(GoogleTranslatorService::class);
        $translator->method('translate')->will($this->returnValue($returnObj));

        $translationController = new TranslationController($translator);
        $translationReq = new TranslationRequest;
        $translationReq->language = "fr";
        $translationReq->expression = "help others";

        $response = $translationController->index($translationReq);

        $this->assertIsObject($response);
        $this->assertEquals($response->getData()->translatedText, "aider les autres");

    }

    /**
     *@return void
     */
    public function test_controller_index_method_translate_text_fail()
    {

        $returnObj = new StdClass;
        $returnObj->code = 400;
        $returnObj->error = "Invalid Value";

        $translator=$this->createMock(GoogleTranslatorService::class);
        $translator->method('translate')->will($this->returnValue($returnObj));

        $translationController = new TranslationController($translator);
        $translationReq = new TranslationRequest;
        $translationReq->language = "frsd";
        $translationReq->expression = "help others";

        $response = $translationController->index($translationReq);

        $this->assertIsObject($response);
        $this->assertEquals($response->getData()->code, 400);
        $this->assertEquals($response->getData()->error, "Invalid Value");

    }
}
