import { Component, EventEmitter, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Subject, Observable } from 'rxjs';
import { debounceTime, switchMap } from 'rxjs/operators';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { DocumentSearchService } from '../../services/document-search.service';
import { DocumentService } from '../../services/document.service';
import { SearchResult } from '../../models/documents';


@Component({
  selector: 'app-search-bar',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MatInputModule,
    MatSelectModule
  ],
  templateUrl: './search-bar.component.html',
  styleUrls: ['./search-bar.component.scss']
})
export class SearchBarComponent {
  query = '';
  sortOption: 'relevance' | 'date' = 'relevance';
  private searchSubject = new Subject<string>();
  results: SearchResult[] = [];

  constructor(
    private docService: DocumentService,
    private searchService: DocumentSearchService

  ) {
    this.searchSubject.pipe(
      debounceTime(300),
      switchMap(q => this.search(q ?? ""))
    ).subscribe(res => {
      this.results = res.results || [];
      this.sortResults();
    });
  }

  onInput() {
    const trimmedQuery = this.query.trim();

    if (!trimmedQuery) {
      this.searchService.clearResults(); 
    } else {
      this.searchSubject.next(trimmedQuery);
    }
  }


  private search(q: string): Observable<any> {
    if (!q) {
      return new Observable(observer => {
        observer.next({ results: [] });
        observer.complete();
      });
    }
    return this.docService.searchDocuments(q);
  }

  sortResults(): any {
    if (this.sortOption === 'relevance') {
      this.results.sort((a, b) => b.relevance - a.relevance);
    } else {
      this.results.sort((a, b) => new Date(b.uploaded_at).getTime() - new Date(a.uploaded_at).getTime());
    }
    this.searchService.setResults(this.results);
    return true;
  }
}
