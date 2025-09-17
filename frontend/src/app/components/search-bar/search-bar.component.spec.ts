import { ComponentFixture, TestBed, fakeAsync, tick } from '@angular/core/testing';
import { SearchBarComponent } from './search-bar.component';
import { DocumentService } from '../../services/document.service';
import { DocumentSearchService } from '../../services/document-search.service';
import { of } from 'rxjs';
import { FormsModule } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { CommonModule } from '@angular/common';

describe('SearchBarComponent', () => {
  let component: SearchBarComponent;
  let fixture: ComponentFixture<SearchBarComponent>;
  let docServiceSpy: jasmine.SpyObj<DocumentService>;
  let searchServiceSpy: jasmine.SpyObj<DocumentSearchService>;

  beforeEach(async () => {
    docServiceSpy = jasmine.createSpyObj('DocumentService', ['searchDocuments']);
    searchServiceSpy = jasmine.createSpyObj('DocumentSearchService', ['clearResults', 'setResults']);

    await TestBed.configureTestingModule({
      imports: [
        SearchBarComponent,
        FormsModule,
        MatInputModule,
        MatSelectModule,
        CommonModule
      ],
      providers: [
        { provide: DocumentService, useValue: docServiceSpy },
        { provide: DocumentSearchService, useValue: searchServiceSpy }
      ]
    }).compileComponents();

    fixture = TestBed.createComponent(SearchBarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should clear results if input is empty', () => {
    component.query = '   ';
    component.onInput();
    expect(searchServiceSpy.clearResults).toHaveBeenCalled();
  });

  it('should trigger search if input is not empty', fakeAsync(() => {
    const mockResults = { results: [{ filename: 'doc1', uploaded_at: '2023-01-01', relevance: 2 }] };
    docServiceSpy.searchDocuments.and.returnValue(of(mockResults));
    component.query = 'test';
    component.onInput();
    tick(300); // debounceTime
    expect(docServiceSpy.searchDocuments).toHaveBeenCalledWith('test');
  }));

  it('should sort results by relevance by default', fakeAsync(() => {
    const mockResults = {
      results: [
        { filename: 'doc1', uploaded_at: '2023-01-01', relevance: 1 },
        { filename: 'doc2', uploaded_at: '2023-01-02', relevance: 3 }
      ]
    };
    docServiceSpy.searchDocuments.and.returnValue(of(mockResults));
    component.query = 'abc';
    component.onInput();
    tick(300);
    expect(component.results[0].filename).toBe('doc2');
    expect(searchServiceSpy.setResults).toHaveBeenCalledWith(component.results);
  }));

  it('should sort results by date when sortOption is date', fakeAsync(() => {
    const mockResults = {
      results: [
        { filename: 'doc1', uploaded_at: '2023-01-01', relevance: 1 },
        { filename: 'doc2', uploaded_at: '2023-01-02', relevance: 3 }
      ]
    };
    docServiceSpy.searchDocuments.and.returnValue(of(mockResults));
    component.query = 'abc';
    component.sortOption = 'date';
    component.onInput();
    tick(300);
    expect(component.results[0].filename).toBe('doc2');
    expect(searchServiceSpy.setResults).toHaveBeenCalledWith(component.results);
  }));
});
