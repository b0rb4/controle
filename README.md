# Sistema de Gerenciamento de Jogos Esportivos

Um sistema web para gerenciar e coordenar transmissões de jogos esportivos, desenvolvido em PHP com interface responsiva.

## 📋 Funcionalidades

- **Página Inicial**: Exibe o jogo do dia ou próximo jogo cadastrado
- **Cadastro de Jogos**: Interface para registrar novos jogos
- **Gerenciador de Eventos**: Painel para administrar todos os eventos
- **Coordenação de Transmissão**: Sistema para coordenar transmissões ao vivo
- **Interface Responsiva**: Adaptável para desktop, tablet e dispositivos móveis

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7.4+ com MySQLi
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Banco de Dados**: MySQL
- **Design**: Interface responsiva com sidebar colapsável

## 📦 Estrutura do Projeto

```
projeto/
├── index.php              # Página inicial
├── cadastrar_jogo.php     # Formulário de cadastro
├── manager.php            # Gerenciador de eventos
├── coordenar.php          # Coordenação de transmissão
└── README.md              # Este arquivo
```

## 🗄️ Estrutura do Banco de Dados

### Tabela: `jogos`

```sql
CREATE TABLE jogos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    time_casa VARCHAR(100) NOT NULL,
    time_fora VARCHAR(100) NOT NULL,
    data_jogo DATE NOT NULL,
    hora_jogo TIME NOT NULL,
    local_jogo VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## ⚙️ Configuração

### 1. Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)

### 2. Configuração do Banco de Dados

```php
// Configurações de conexão no arquivo index.php
$mysqli = new mysqli("localhost", "manager", "102030", "manager");
```

**Altere as credenciais conforme seu ambiente:**
- **Host**: localhost (ou IP do servidor)
- **Usuário**: manager (seu usuário MySQL)
- **Senha**: 102030 (sua senha MySQL)
- **Database**: manager (nome do banco de dados)

### 3. Instalação

1. Clone ou baixe o projeto
2. Configure o banco de dados MySQL
3. Crie a tabela `jogos` usando o SQL fornecido
4. Ajuste as credenciais de conexão no código
5. Hospede os arquivos em seu servidor web

## 🎯 Como Usar

### Página Inicial
- Acesse `index.php` para ver o próximo jogo
- Use o botão "Coordenar Transmissão" para iniciar uma transmissão

### Navegação
- **Menu Lateral**: Clique no ícone ☰ para expandir/recolher
- **Mobile**: Menu overlay com botão fixo no topo
- **Teclado**: Use Tab, Enter e setas para navegar

### Funcionalidades do Menu
- 🏠 **Página Inicial**: Visualizar próximo jogo
- ✏️ **Cadastrar Jogo**: Adicionar novos jogos
- 📅 **Gerenciador**: Administrar todos os eventos

## 📱 Responsividade

O sistema é totalmente responsivo com breakpoints para:

- **Desktop** (1200px+): Layout completo com sidebar
- **Tablet** (768px - 1199px): Layout adaptado
- **Mobile** (480px - 767px): Menu overlay
- **Small Mobile** (até 320px): Layout otimizado

### Recursos Mobile
- Menu lateral deslizante
- Overlay escuro de fundo
- Botão toggle fixo no topo
- Navegação por gestos
- Fechamento por ESC

## 🎨 Características da Interface

### Design
- **Tema Escuro**: Cores principais #2d2d44 e #3a3a59
- **Destaque**: Roxo (#6c5ce7) e ciano (#81ecec)
- **Tipografia**: Segoe UI com fallbacks
- **Sombras e Efeitos**: Box-shadow com transparência

### Acessibilidade
- Navegação por teclado completa
- Foco visível em todos os elementos
- Contraste adequado de cores
- Textos responsivos com clamp()
- Área mínima de toque de 44px

## 🔧 Personalização

### Cores
Edite as variáveis CSS para alterar o esquema de cores:

```css
/* Cores principais */
background-color: #2d2d44;  /* Fundo principal */
background-color: #3a3a59;  /* Sidebar e cards */
color: #6c5ce7;             /* Destaque primário */
color: #81ecec;             /* Destaque secundário */
```

### Layout
Ajuste os breakpoints no CSS para diferentes necessidades:

```css
@media (max-width: 768px) { /* Tablets */ }
@media (max-width: 480px) { /* Mobile */ }
```

## 🔒 Segurança

O código implementa medidas básicas de segurança:

- **Prepared Statements**: Prevenção contra SQL Injection
- **htmlspecialchars()**: Escape de HTML para prevenir XSS
- **Validação de Entrada**: Verificação de dados do usuário

### Recomendações Adicionais
- Implemente autenticação de usuários
- Use HTTPS em produção
- Configure headers de segurança
- Valide e sanitize todas as entradas
- Implemente rate limiting

## 🚀 Melhorias Futuras

- [ ] Sistema de autenticação
- [ ] API RESTful
- [ ] Notificações em tempo real
- [ ] Relatórios e estatísticas
- [ ] Integração com redes sociais
- [ ] Sistema de comentários
- [ ] Upload de imagens
- [ ] Múltiplos idiomas

## 📄 Licença

Este projeto está sob licença livre para uso pessoal e educacional.

## 🤝 Contribuição

Contribuições são bem-vindas! Para contribuir:

1. Faça um fork do projeto
2. Crie uma branch para sua feature
3. Commit suas alterações
4. Push para a branch
5. Abra um Pull Request

## 📞 Suporte

Para dúvidas ou problemas:
- Verifique a documentação
- Confira as configurações do banco
- Teste as credenciais de conexão
- Valide as permissões dos arquivos

---
