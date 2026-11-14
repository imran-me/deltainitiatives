<?php

namespace Modules\Admin\Support;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Core\Support\Icons;

/**
 * A form/table field for an admin Resource.
 *
 *   Field::text('name')->required()->onIndex(),
 *   Field::select('status')->options([...])->onIndex(),
 *   Field::markdown('body')->help('Markdown supported'),
 *   Field::image('photo_path')->directory('team'),
 *   Field::boolean('is_published')->default(true)->onIndex(),
 */
class Field
{
    public string $label;

    public bool $required = false;

    public array $rules = [];

    public ?string $help = null;

    public array $options = [];

    public bool $onIndex = false;

    public ?string $placeholder = null;

    public int $rows = 5;

    public string $directory = 'uploads';

    public string $disk = 'public';

    public array $mimes = [];

    public int $maxKb = 4096;

    public bool $readonly = false;

    public bool $hideOnForm = false;

    public bool $hideOnDetail = false;

    public string $span = 'full';

    public ?int $maxLength = null;

    public bool $unique = false;

    public mixed $default = null;

    public ?Closure $displayUsing = null;

    public ?string $section = null;

    public function __construct(public string $name, public string $type = 'text')
    {
        $this->label = Str::of($name)->replaceLast('_path', '')->replaceLast('_id', '')->headline()->toString();
    }

    public static function make(string $name, string $type = 'text'): static
    {
        return new static($name, $type);
    }

    public static function text(string $name): static { return new static($name, 'text'); }
    public static function email(string $name): static { return new static($name, 'email'); }
    public static function url(string $name): static { return new static($name, 'url'); }
    public static function number(string $name): static { return new static($name, 'number'); }
    public static function textarea(string $name): static { return new static($name, 'textarea'); }
    public static function markdown(string $name): static { return (new static($name, 'markdown'))->rows(12); }
    public static function select(string $name): static { return new static($name, 'select'); }
    public static function boolean(string $name): static { return new static($name, 'boolean'); }
    public static function date(string $name): static { return new static($name, 'date'); }
    public static function datetime(string $name): static { return new static($name, 'datetime'); }
    public static function image(string $name): static { return (new static($name, 'image'))->mimes(['jpg', 'jpeg', 'png', 'webp']); }
    public static function file(string $name): static { return (new static($name, 'file'))->mimes(['pdf'])->maxKb(20480); }
    public static function icon(string $name): static { return (new static($name, 'select'))->options(array_combine(Icons::names(), Icons::names()))->help('Icon name from the Lucide set (lucide.dev).'); }
    /** Read-only text value (shown on detail pages, not editable). Equivalent to Field::text($name)->readonly(). */
    public static function info(string $name): static { return (new static($name, 'text'))->readonly(); }

    public function label(string $label): static { $this->label = $label; return $this; }
    public function required(bool $required = true): static { $this->required = $required; return $this; }
    public function rules(array $rules): static { $this->rules = $rules; return $this; }
    public function help(string $help): static { $this->help = $help; return $this; }
    public function options(array $options): static { $this->options = $options; return $this; }
    public function onIndex(bool $onIndex = true): static { $this->onIndex = $onIndex; return $this; }
    public function placeholder(string $placeholder): static { $this->placeholder = $placeholder; return $this; }
    public function rows(int $rows): static { $this->rows = $rows; return $this; }
    public function directory(string $directory): static { $this->directory = $directory; return $this; }
    public function disk(string $disk): static { $this->disk = $disk; return $this; }
    public function mimes(array $mimes): static { $this->mimes = $mimes; return $this; }
    public function maxKb(int $kb): static { $this->maxKb = $kb; return $this; }
    public function hideOnForm(bool $hide = true): static { $this->hideOnForm = $hide; return $this; }
    public function hideOnDetail(bool $hide = true): static { $this->hideOnDetail = $hide; return $this; }
    public function half(): static { $this->span = 'half'; return $this; }
    public function maxLength(int $length): static { $this->maxLength = $length; return $this; }
    public function unique(bool $unique = true): static { $this->unique = $unique; return $this; }
    public function default(mixed $value): static { $this->default = $value; return $this; }
    public function displayUsing(Closure $callback): static { $this->displayUsing = $callback; return $this; }
    public function section(string $section): static { $this->section = $section; return $this; }

    public function readonly(bool $readonly = true): static
    {
        $this->readonly = $readonly;

        return $this;
    }

    public function isUpload(): bool
    {
        return in_array($this->type, ['image', 'file'], true);
    }

    /** @return array<int, mixed> */
    public function validationRules(?Model $record, string $table): array
    {
        if ($this->readonly || $this->hideOnForm) {
            return [];
        }

        $isNew = ! $record?->exists;
        $presence = $this->required && (! $this->isUpload() || $isNew || blank($record?->{$this->name})) ? 'required' : 'nullable';

        $rules = match ($this->type) {
            'boolean' => ['nullable', 'boolean'],
            'email' => [$presence, 'email', 'max:255'],
            'url' => [$presence, 'url:http,https', 'max:500'],
            'number' => [$presence, 'numeric', 'min:-999999999', 'max:999999999'],
            'select' => [$presence, Rule::in(array_map('strval', array_keys($this->options)))],
            'date', 'datetime' => [$presence, 'date'],
            'textarea', 'markdown' => [$presence, 'string', 'max:'.($this->maxLength ?? 50000)],
            'image' => [$presence, 'image', 'mimes:'.implode(',', $this->mimes), 'max:'.$this->maxKb],
            'file' => [$presence, 'file', 'mimes:'.implode(',', $this->mimes), 'max:'.$this->maxKb],
            default => [$presence, 'string', 'max:'.($this->maxLength ?? 255)],
        };

        if ($this->unique) {
            $rules[] = Rule::unique($table, $this->name)->ignore($record?->getKey());
        }

        return array_merge($rules, $this->rules);
    }

    public function fill(Model $record, Request $request): void
    {
        if ($this->readonly || $this->hideOnForm) {
            return;
        }

        switch ($this->type) {
            case 'boolean':
                $record->{$this->name} = $request->boolean($this->name);
                break;

            case 'image':
            case 'file':
                if ($request->hasFile($this->name)) {
                    $this->deleteStored($record);
                    $record->{$this->name} = $request->file($this->name)->store($this->directory, $this->disk);
                } elseif ($request->boolean('remove_'.$this->name)) {
                    $this->deleteStored($record);
                    $record->{$this->name} = null;
                }
                break;

            case 'datetime':
                $value = $request->input($this->name);
                $record->{$this->name} = filled($value)
                    ? Carbon::parse($value, config('app.display_timezone'))->utc()
                    : null;
                break;

            case 'number':
                $value = $request->input($this->name);
                $record->{$this->name} = $value === null || $value === '' ? null : $value + 0;
                break;

            default:
                $value = $request->input($this->name);
                $record->{$this->name} = is_string($value) ? trim($value) : $value;
        }
    }

    public function deleteStored(Model $record): void
    {
        if ($this->isUpload() && filled($path = $record->getOriginal($this->name))) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    /** Value used to pre-fill the form control. */
    public function formValue(Model $record): mixed
    {
        $value = $record->exists ? $record->{$this->name} : ($record->{$this->name} ?? $this->default);

        return match ($this->type) {
            'datetime' => $value ? Carbon::parse($value)->setTimezone(config('app.display_timezone'))->format('Y-m-d\TH:i') : null,
            'date' => $value ? Carbon::parse($value)->format('Y-m-d') : null,
            'boolean' => (bool) ($value ?? $this->default),
            default => $value,
        };
    }

    /** Value rendered in tables and detail views. */
    public function display(Model $record, bool $full = false): string|HtmlString
    {
        if ($this->displayUsing) {
            return ($this->displayUsing)($record, $full);
        }

        $value = $record->{$this->name};

        if ($value === null || $value === '') {
            return new HtmlString('<span class="text-navy-300">—</span>');
        }

        return match ($this->type) {
            'boolean' => new HtmlString($value
                ? '<span class="inline-flex items-center gap-1 rounded-full bg-river-50 px-2 py-0.5 text-xs font-medium text-river-800 ring-1 ring-river-200">Yes</span>'
                : '<span class="inline-flex items-center gap-1 rounded-full bg-navy-50 px-2 py-0.5 text-xs font-medium text-navy-500 ring-1 ring-navy-100">No</span>'),
            'image' => new HtmlString('<img src="'.e(Storage::disk($this->disk)->url($value)).'" alt="" class="h-10 w-auto max-w-24 rounded-md object-contain ring-1 ring-navy-100">'),
            'file' => new HtmlString('<span class="font-mono text-xs">'.e(basename($value)).'</span>'),
            'markdown' => $full ? markdown($value) : Str::limit(strip_tags((string) $value), 90),
            'url' => new HtmlString('<a href="'.e($value).'" target="_blank" rel="noopener noreferrer" class="text-river-700 underline underline-offset-2 break-all">'.e(Str::limit($value, 48)).'</a>'),
            'email' => new HtmlString('<a href="mailto:'.e($value).'" class="text-river-700 underline underline-offset-2 break-all">'.e($value).'</a>'),
            'textarea' => $full ? new HtmlString(nl2br(e($value))) : Str::limit((string) $value, 90),
            // Plain strings are escaped once, by Blade's {{ }} in the view.
            'select' => (string) ($this->options[$value] ?? $value),
            'datetime' => dhaka_time(Carbon::parse($value)),
            'date' => Carbon::parse($value)->format('j M Y'),
            default => Str::limit((string) $value, $full ? 5000 : 80),
        };
    }
}
