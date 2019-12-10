/**
 * Gota
 *
 * Entidade Gota
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-10-01
 *
 */

class Gota {
    constructor(id, nomeParametro, multiplicadorGota, habilitado, tipoCadastro, auditInsert = null, auditUpdate = null) {
        this.id = id;
        this.nomeParametro = nomeParametro;
        this.multiplicadorGota = multiplicadorGota;
        this.habilitado = habilitado;
        this.tipoCadastro = tipoCadastro;
        this.auditInsert = auditInsert;
        this.auditUpdate = auditUpdate;
    }
}
