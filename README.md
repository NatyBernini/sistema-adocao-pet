# 🐾 Pet-Adote – Sistema de Apoio à Adoção de Animais

## 📌 Descrição do Projeto

O **Pet-Adote** é um sistema web desenvolvido para facilitar e organizar o processo de adoção de animais entre **ONGs** e **adotantes**.  
A plataforma possibilita o cadastro de animais, envio de solicitações de adoção e gerenciamento completo por parte das ONGs.

Seu objetivo é tornar a adoção responsável mais acessível, rápida e segura.

---

## 🚀 Funcionalidades

### 🏢 Usuário ONG (Admin)
- Cadastro e login do administrador.
- Cadastro de animais disponíveis para adoção.
- Edição e exclusão de animais.
- Upload de foto do animal.
- Visualização das solicitações de adoção.
- Aprovação ou rejeição das solicitações.
- Histórico de adoções concluídas.

### 👤 Usuário Adotante
- Cadastro completo com integração automática ViaCEP.
- Login seguro.
- Navegação pelos animais cadastrados.
- Envio de solicitações de adoção.
- Acompanhamento do status da solicitação.

---

## 🛠 Tecnologias Utilizadas

- **PHP (PDO)**
- **MySQL**
- **HTML5 / CSS3**
- **JavaScript (Fetch API / ViaCEP)**
- **XAMPP (Apache + MySQL)**
- **GitHub (versões e colaboração)**

---

## 📂 Como Rodar o Projeto

1. Instalar o **XAMPP**
2. Ligar o **Apache** e **MySQL**
3. Criar o banco de dados executando o arquivo `login.sql`
4. Colocar o projeto na pasta:  

5. Acessar no navegador:  
👉 http://localhost/login/index.php

---

## 🔐 Usuários e Perfis

⚠️ O sistema **não possui usuários pré-cadastrados**.

Você pode criar um novo usuário pela própria tela de cadastro.

Perfis disponíveis:

- **admin** → Acesso total ao sistema  
- **adotante** → Pode solicitar adoções

---

## 🔄 Fluxo do Sistema

1. Fazer cadastro ou login  
2. Redirecionamento automático:
- Admin → Dashboard
- Adotante → Lista de animais
3. O Admin gerencia:
- Animais
- Solicitações
- Aprovações / Rejeições
4. O Adotante:
- Visualiza os animais
- Envia solicitações
- Acompanha o status

---

## 🐶 CRUD de Animais (Admin)

- Cadastro ✔  
- Edição ✔  
- Exclusão ✔  
- Upload de foto ✔  
- Histórico de adoções ✔  

---

## 👥 Equipe

| Nome | Função |
|------|--------|
| **Natália Bernini** | Desenvolvedora Frontend / UI-UX |
| **Gabriel Tolentino** | Desenvolvedor Backend |
| **Matheus Lima** | Analista de Dados |

---

## 💙 Mensagem Final

> “Cada linha de código deste projeto não criou apenas um sistema —  
> mas ajudou a aproximar animais abandonados de famílias que podem transformar suas vidas.”

---
