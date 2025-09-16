import { Component, EventEmitter, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormControl } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { HttpClientModule, HttpClient } from '@angular/common/http';
import { debounceTime, switchMap } from 'rxjs/operators';
import { Observable, of } from 'rxjs';

@Component({
  selector: 'app-search-bar',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    MatInputModule,
    MatFormFieldModule,
    MatAutocompleteModule
  ],
  templateUrl: './search-bar.component.html',
  styleUrls: ['./search-bar.component.scss']
})
export class SearchBarComponent {
  searchControl = new FormControl('');
  results: any[] = [];
  // @Output() selectResult = new EventEmitter<any>();

  constructor(private http: HttpClient) {
    // Debounced real-time search
    this.searchControl.valueChanges.pipe(
      debounceTime(300),
      switchMap(q => this.searchDocuments(q ?? ""))
    ).subscribe(res => {
      this.results = res || [];
    });
  }

  searchDocuments(query: string): Observable<any[]> {
    if (!query || query.trim().length < 2) return of([]);
    return this.http.get<any>(`http://localhost:8080/api/search/?q=${encodeURIComponent(query)}`)
      .pipe(
        switchMap(res => of(res.results || []))
      );
  }

  selectItem(item: any) {
    this.searchControl.setValue(item.filename);
    // this.selectResult.emit(item);
  }

  onBlur() {
    // optional: delay hiding dropdown if needed
    // mat-autocomplete handles this automatically
  }
}
