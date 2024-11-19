<?php

namespace Jairosantos\GabineteDigital\Models;

use Jairosantos\GabineteDigital\Core\Database;
use PDO;

class Proposicao {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }


    public function inserirProposicao($dados) {

        $ano = $dados[0]['proposicao_ano'];

        $deleteQuery = "DELETE FROM proposicoes WHERE proposicao_ano = :proposicao_ano";
        $deleteStmt = $this->conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':proposicao_ano', $ano, PDO::PARAM_INT);
        $deleteStmt->execute();

        $query = "INSERT INTO proposicoes (proposicao_id, proposicao_numero, proposicao_titulo, proposicao_ano, proposicao_tipo, proposicao_ementa, proposicao_apresentacao, proposicao_arquivada) VALUES (:proposicao_id, :proposicao_numero, :proposicao_titulo, :proposicao_ano, :proposicao_tipo, :proposicao_ementa, :proposicao_apresentacao, :proposicao_arquivada)";

        $stmt = $this->conn->prepare($query);

        foreach ($dados as $proposicao) {
            $stmt->bindParam(':proposicao_id', $proposicao['proposicao_id'], PDO::PARAM_INT);
            $stmt->bindParam(':proposicao_numero', $proposicao['proposicao_numero'], PDO::PARAM_INT);
            $stmt->bindParam(':proposicao_titulo', $proposicao['proposicao_titulo']);
            $stmt->bindParam(':proposicao_ano', $proposicao['proposicao_ano'], PDO::PARAM_INT);
            $stmt->bindParam(':proposicao_tipo', $proposicao['proposicao_tipo']);
            $stmt->bindParam(':proposicao_ementa', $proposicao['proposicao_ementa']);
            $stmt->bindParam(':proposicao_apresentacao', $proposicao['proposicao_apresentacao']);
            $stmt->bindParam(':proposicao_arquivada', $proposicao['proposicao_arquivada'], PDO::PARAM_INT);

            $stmt->execute();
        }

        return true;
    }


    public function inserirProposicaoAutor($dados) {

        $ano = $dados[0]['proposicao_autor_ano'];

        $deleteQuery = "DELETE FROM proposicoes_autores WHERE proposicao_autor_ano = :proposicao_autor_ano";
        $deleteStmt = $this->conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':proposicao_autor_ano', $ano, PDO::PARAM_INT);
        $deleteStmt->execute();

        $query = "INSERT INTO proposicoes_autores (proposicao_id, proposicao_autor_id, proposicao_autor_nome, proposicao_autor_partido, proposicao_autor_estado, proposicao_autor_proponente, proposicao_autor_assinatura, proposicao_autor_ano) VALUES (:proposicao_id, :proposicao_autor_id, :proposicao_autor_nome, :proposicao_autor_partido, :proposicao_autor_estado, :proposicao_autor_proponente, :proposicao_autor_assinatura, :proposicao_autor_ano)";

        $stmt = $this->conn->prepare($query);

        foreach ($dados as $proposicao) {
            $stmt->bindParam(':proposicao_id', $proposicao['proposicao_id'], PDO::PARAM_INT);
            $stmt->bindParam(':proposicao_autor_id', $proposicao['proposicao_autor_id'], PDO::PARAM_INT);
            $stmt->bindParam(':proposicao_autor_nome', $proposicao['proposicao_autor_nome']);
            $stmt->bindParam(':proposicao_autor_partido', $proposicao['proposicao_autor_partido']);
            $stmt->bindParam(':proposicao_autor_estado', $proposicao['proposicao_autor_estado']);
            $stmt->bindParam(':proposicao_autor_proponente', $proposicao['proposicao_autor_proponente'], PDO::PARAM_INT);
            $stmt->bindParam(':proposicao_autor_assinatura', $proposicao['proposicao_autor_assinatura'], PDO::PARAM_INT);
            $stmt->bindParam(':proposicao_autor_ano', $proposicao['proposicao_autor_ano'], PDO::PARAM_INT);

            $stmt->execute();
        }

        return true;
    }
}