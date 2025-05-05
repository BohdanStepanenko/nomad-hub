# Codequotient - AI-Powered Career Path Advisor

A smart platform to assess your skills, analyze job market demands, and generate personalized learning roadmaps using AI.

## Key Features (MVP)

- **Skill Assessment**: Self-evaluation checklists and automated tests to gauge your proficiency.
- **Job Market Insights**: Real-time parsing and analysis of IT vacancies (Python, DevOps, Data Engineer) via **ElasticSearch**.
- **AI-Driven Roadmaps**: OpenAI-generated learning paths prioritizing high-impact, low-effort skills.
- **Course Integration**: Curated resources (Coursera, Udemy) tailored to your goals.
- **Performance Optimization**: **Redis** caching for faster recommendations.

## Tech Stack

- **Backend**: Laravel 10
- **AI**: OpenAI API (GPT-4)
- **Search**: ElasticSearch
- **Cache**: Redis
- **Database**: MySQL
- **Storage**: AWS S3 (for avatars/resources)
- **Frontend**: React