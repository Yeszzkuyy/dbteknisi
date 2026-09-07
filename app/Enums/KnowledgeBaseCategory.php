<?php

namespace App\Enums;

enum KnowledgeBaseCategory: string
{
    case Sop = 'sop';
    case ProjectDocumentation = 'project-documentation';
    case Proposal = 'proposal';
    case TechnicalDocumentation = 'technical-documentation';
    case Reports = 'reports';
    case CompanyKnowledge = 'company-knowledge';

    public function label(): string
    {
        return match ($this) {
            self::Sop => 'SOP',
            self::ProjectDocumentation => 'Project Documentation',
            self::Proposal => 'Proposal',
            self::TechnicalDocumentation => 'Technical Documentation',
            self::Reports => 'Reports',
            self::CompanyKnowledge => 'Company Knowledge',
        };
    }
}
