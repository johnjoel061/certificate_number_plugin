<?php
// This file is part of the tool_certificate plugin for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the version information for the code plugin.
 *
 * @package    certificateelement_certificatenumber
 * @copyright  2025 John Joel Alfabete <example@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace certificateelement_certificatenumber;

 use advanced_testcase;
 use tool_certificate_generator;
 use moodle_url;
 use core_text;
 
 /**
  * Unit tests for certificate number element.
  */
 final class element_test extends advanced_testcase {
 
     /**
      * Test setup.
      */
     public function setUp(): void {
         parent::setUp();
         $this->resetAfterTest();
     }
 
     /**
      * Get certificate generator.
      * @return tool_certificate_generator
      */
     protected function get_generator(): tool_certificate_generator {
         return $this->getDataGenerator()->get_plugin_generator('tool_certificate');
     }
 
     /**
      * Test render_html_content.
      */
     public function test_render_html_content(): void {
         $certificate = $this->get_generator()->create_template((object)['name' => 'Test Certificate']);
         $pageid = $this->get_generator()->create_page($certificate)->get_id();
         $element = $this->get_generator()->create_element($pageid, 'certificatenumber',
             ['display' => \certificateelement_certificatenumber\element::DISPLAY_CERTIFICATENUMBER]);
 
         // Ensure it renders a certificate number.
         $output = strip_tags($element->render_html());
         $this->assertMatchesRegularExpression('/^[0-9]+$/', $output);
     }
 
     /**
      * Test certificate number generation.
      */
     public function test_generate_certificate_number(): void {
         $element = $this->getMockBuilder(element::class)
             ->disableOriginalConstructor()
             ->getMock();
 
         $reflection = new \ReflectionMethod($element, 'generate_certificate_number');
         $reflection->setAccessible(true);
 
         $certificatenumber = $reflection->invoke($element);
         
         // Ensure the certificate number is a positive integer.
         $this->assertIsInt($certificatenumber);
         $this->assertGreaterThan(0, $certificatenumber);
     }
 
     /**
      * Test certificate number database insertion.
      */
     public function test_certificate_number_db_insert(): void {
         global $DB;
 
         $certificate = $this->get_generator()->create_template((object)['name' => 'Test Certificate']);
         $issue = $this->get_generator()->issue($certificate, $this->getDataGenerator()->create_user());
         
         $element = $this->getMockBuilder(element::class)
             ->disableOriginalConstructor()
             ->getMock();
 
         $reflection = new \ReflectionMethod($element, 'certificate_number_db_insert');
         $reflection->setAccessible(true);
 
         $certificatenumber = $reflection->invoke($element, $issue->id);
         
         // Ensure the certificate number was inserted.
         $record = $DB->get_record('tool_certificate_number', ['certificatenumber' => $certificatenumber]);
         $this->assertNotEmpty($record);
     }
 }
 