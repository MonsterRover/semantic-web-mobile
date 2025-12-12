<?php

namespace App\Services;

use EasyRdf\Graph;
use EasyRdf\Sparql\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OntologyService
{
    protected $graph;
    protected $sparqlClient;
    protected $ontologyPath;

    public function __construct()
    {
        // Initialize EasyRDF
        \EasyRdf\RdfNamespace::set('skripsi', 'http://www.semanticweb.org/skripsi#');
        
        $this->ontologyPath = storage_path('app/ontology');
        
        // Create ontology directory if not exists
        if (!file_exists($this->ontologyPath)) {
            mkdir($this->ontologyPath, 0755, true);
        }
    }

    /**
     * Load ontology from file
     */
    public function loadOntology($filePath = null)
    {
        try {
            if (!$filePath) {
                $filePath = $this->ontologyPath . '/' . env('ONTOLOGY_FILE', 'skripsi-ontology.owl');
            }

            if (!file_exists($filePath)) {
                Log::warning("Ontology file not found: {$filePath}");
                return null;
            }

            $this->graph = new Graph();
            $this->graph->parseFile($filePath);

            Log::info("Ontology loaded successfully from: {$filePath}");
            return $this->graph;
        } catch (\Exception $e) {
            Log::error("Error loading ontology: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Execute SPARQL query
     */
    public function executeSparqlQuery($query)
    {
        try {
            if (!$this->graph) {
                $this->loadOntology();
            }

            if (!$this->graph) {
                return [];
            }

            $results = $this->graph->query($query);
            return $results;
        } catch (\Exception $e) {
            Log::error("SPARQL query error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get related topics from ontology
     */
    public function getRelatedTopics($topic)
    {
        try {
            if (!$this->graph) {
                $this->loadOntology();
            }

            if (!$this->graph) {
                return [];
            }

            // SPARQL query to find related topics
            $query = "
                PREFIX skripsi: <http://www.semanticweb.org/skripsi#>
                PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                
                SELECT DISTINCT ?relatedTopic ?label
                WHERE {
                    {
                        ?topic rdfs:label ?topicLabel .
                        FILTER (regex(?topicLabel, '{$topic}', 'i'))
                        ?topic skripsi:relatedTo ?relatedTopic .
                        ?relatedTopic rdfs:label ?label .
                    }
                    UNION
                    {
                        ?topic rdfs:label ?topicLabel .
                        FILTER (regex(?topicLabel, '{$topic}', 'i'))
                        ?relatedTopic skripsi:relatedTo ?topic .
                        ?relatedTopic rdfs:label ?label .
                    }
                    UNION
                    {
                        ?topic rdfs:label ?topicLabel .
                        FILTER (regex(?topicLabel, '{$topic}', 'i'))
                        ?subTopic skripsi:subTopicOf ?topic .
                        ?subTopic rdfs:label ?label .
                        BIND(?subTopic AS ?relatedTopic)
                    }
                }
            ";

            $results = $this->executeSparqlQuery($query);
            
            $relatedTopics = [];
            foreach ($results as $result) {
                if (isset($result->label)) {
                    $relatedTopics[] = (string) $result->label;
                }
            }

            return array_unique($relatedTopics);
        } catch (\Exception $e) {
            Log::error("Error getting related topics: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get topic hierarchy (parent and children)
     */
    public function getTopicHierarchy($topic)
    {
        try {
            if (!$this->graph) {
                $this->loadOntology();
            }

            if (!$this->graph) {
                return ['parent' => null, 'children' => []];
            }

            $query = "
                PREFIX skripsi: <http://www.semanticweb.org/skripsi#>
                PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                
                SELECT ?parent ?child ?parentLabel ?childLabel
                WHERE {
                    {
                        ?topicRes rdfs:label ?topicLabel .
                        FILTER (regex(?topicLabel, '{$topic}', 'i'))
                        ?topicRes skripsi:subTopicOf ?parent .
                        ?parent rdfs:label ?parentLabel .
                    }
                    UNION
                    {
                        ?topicRes rdfs:label ?topicLabel .
                        FILTER (regex(?topicLabel, '{$topic}', 'i'))
                        ?child skripsi:subTopicOf ?topicRes .
                        ?child rdfs:label ?childLabel .
                    }
                }
            ";

            $results = $this->executeSparqlQuery($query);
            
            $hierarchy = ['parent' => null, 'children' => []];
            
            foreach ($results as $result) {
                if (isset($result->parentLabel)) {
                    $hierarchy['parent'] = (string) $result->parentLabel;
                }
                if (isset($result->childLabel)) {
                    $hierarchy['children'][] = (string) $result->childLabel;
                }
            }

            return $hierarchy;
        } catch (\Exception $e) {
            Log::error("Error getting topic hierarchy: " . $e->getMessage());
            return ['parent' => null, 'children' => []];
        }
    }

    /**
     * Save uploaded ontology file
     */
    public function saveOntologyFile($file, $filename = null)
    {
        try {
            if (!$filename) {
                $filename = 'skripsi-ontology-' . time() . '.owl';
            }

            $path = $file->storeAs('ontology', $filename);
            
            Log::info("Ontology file saved: {$path}");
            return storage_path('app/' . $path);
        } catch (\Exception $e) {
            Log::error("Error saving ontology file: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate ontology file
     */
    public function validateOntologyFile($filePath)
    {
        try {
            $testGraph = new Graph();
            $testGraph->parseFile($filePath);
            return true;
        } catch (\Exception $e) {
            Log::error("Ontology validation failed: " . $e->getMessage());
            return false;
        }
    }
}
