-- ============================================================
-- Portfolio Database — Schema + Seed Data
-- Compatible with MySQL 5.7+ / 8.x (InfinityFree friendly)
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Table: projects
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `short_description` VARCHAR(300) NOT NULL,
  `long_description` TEXT NOT NULL,
  `tech_stack` VARCHAR(300) NOT NULL,
  `github_url` VARCHAR(300) DEFAULT NULL,
  `live_url` VARCHAR(300) DEFAULT NULL,
  `image_url` VARCHAR(300) DEFAULT NULL,
  `category` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: contact_messages
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: admin_users
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seed Data
-- ============================================================

-- Admin: username=admin / password=Portfolio2026!
INSERT INTO `admin_users` (`username`, `password_hash`) VALUES
('admin', '$2y$10$ueJt6J1G5lFvMtk2dLkhbOA5cUYCwxp.w7CLyltReVvCSVK2S8cxS');

INSERT INTO `projects`
(`title`, `short_description`, `long_description`, `tech_stack`, `github_url`, `live_url`, `image_url`, `category`)
VALUES
(
  'Kirax',
  'Multi-agent real estate intelligence platform combining vector search with conversational AI.',
  'Kirax is a real estate intelligence platform that surfaces property insights from heterogeneous data sources. The system orchestrates multiple specialised agents through LangGraph, retrieves contextual knowledge from a Pinecone vector store, and exposes a conversational interface backed by a Clean Architecture Python codebase. It demonstrates production patterns for retrieval-augmented generation, agent routing, and domain-driven design.',
  'LangGraph, Pinecone, Python, RAG, Clean Architecture',
  'https://github.com/erenakay1',
  NULL,
  'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80',
  'AI'
),
(
  'VibeCheck',
  'Location-based social app with real-time emoji reactions and neighborhood leaderboards.',
  'VibeCheck is a real-time, location-aware social application. Users drop emoji-based reactions tied to physical places, see neighborhood leaderboards, and follow live activity through SignalR streams. The backend uses .NET with PostgreSQL + PostGIS for geospatial queries, Redis for hot caches, Firebase for push notifications, and a Docker-based deployment pipeline. The system integrates Google Places for venue resolution and enforces rate limits to keep the experience playful but safe.',
  '.NET, PostgreSQL, PostGIS, Redis, Firebase, SignalR, Docker',
  'https://github.com/erenakay1',
  NULL,
  'https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=800&q=80',
  'Mobile'
),
(
  'HUGIP AI Assistant',
  'RAG-powered AI assistant for Halic University''s entrepreneurship club (HUGIP).',
  'A retrieval-augmented AI assistant built for HUGIP, the entrepreneurship club at Halic University. The system blends a Pinecone vector store with LangChain orchestration, routes ambiguous questions through a web-search fallback, and includes guardrails against prompt injection. A Streamlit UI handles chat sessions, while Supabase persists conversations and user metadata. Designed to be operated by non-technical club members.',
  'LangChain, Pinecone, Streamlit, Supabase, Python',
  'https://github.com/erenakay1',
  NULL,
  'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&q=80',
  'AI'
),
(
  'EaseApp',
  'Enterprise multi-tenant Dataverse integration platform with custom analytics widgets.',
  'EaseApp is a multi-tenant SaaS platform that integrates with Microsoft Dataverse. It ships widget-based data pipelines, workspace-scoped access control, metadata synchronisation, and a global search experience across tenants. The frontend renders custom chart implementations (Sankey, Bubble, StackedRadialBar) on top of an ASP.NET Core + EF Core backend with a PostgreSQL store. The system separates tenant data with row-level security and supports per-tenant schema extensions.',
  'ASP.NET Core, PostgreSQL, Dataverse, EF Core',
  'https://github.com/erenakay1',
  NULL,
  'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&q=80',
  'Web'
),
(
  'UtaiSOFT Tool Search',
  'Production tool retrieval for 1000+ tool LLM agentic systems — ~93-94% P@1.',
  'A dynamic tool selection module designed for LLM agents that need to choose from 1000+ tools at runtime. The system uses gemini-embedding-002 vectors stored in Qdrant, an L4 hierarchical metadata enrichment layer, and BAAI/bge-reranker-v2-m3 cross-encoder reranking. End-to-end retrieval achieves approximately 93-94% Precision@1 in production. The module integrates cleanly with LangGraph-based agent runtimes and is the core of UtaiSOFT''s agentic platform.',
  'Qdrant, gemini-embedding-002, Python, Cross-encoder reranking, LangGraph',
  'https://github.com/erenakay1',
  NULL,
  'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&q=80',
  'AI'
);

SET FOREIGN_KEY_CHECKS = 1;
