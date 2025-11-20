<?php

namespace App\Filament\Resources\ArticleNews;

use App\Filament\Resources\ArticleNews\Pages\CreateArticleNews;
use App\Filament\Resources\ArticleNews\Pages\EditArticleNews;
use App\Filament\Resources\ArticleNews\Pages\ListArticleNews;
use App\Filament\Resources\ArticleNews\Schemas\ArticleNewsForm;
use App\Filament\Resources\ArticleNews\Tables\ArticleNewsTable;
use App\Models\ArticleNews;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArticleNewsResource extends Resource
{
    protected static ?string $model = ArticleNews::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $recordTitleAttribute = 'name';

    // 🧱 FORM
    public static function form(Schema $schema): Schema
    {
        return ArticleNewsForm::configure($schema)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->required(),

                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('author_id')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('is_featured')
                    ->options([
                        'featured' => 'Featured',
                        'not_featured' => 'Not Featured',
                    ])
                    ->required(),

                Forms\Components\RichEditor::make('content')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->required(),
            ]);
    }

    // 📋 TABLE
    public static function table(Table $table): Table
    {
        return ArticleNewsTable::configure($table)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('is_featured')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'featured' => 'success',
                        'not_featured' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category'),

                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail'),
            ])
            ->filters([]);
    }

    // 🔗 RELATION
    public static function getRelations(): array
    {
        return [];
    }

    // 📄 PAGES
    public static function getPages(): array
    {
        return [
            'index' => ListArticleNews::route('/'),
            'create' => CreateArticleNews::route('/create'),
            'edit' => EditArticleNews::route('/{record}/edit'),
        ];
    }

    // 🚫 Hilangkan SoftDeletingScope agar bisa lihat semua data
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
