import { ComponentFixture, TestBed, fakeAsync, tick } from '@angular/core/testing';
import { DocumentListComponent } from './document-list.component';
import { MatDialog } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Subject, of, throwError } from 'rxjs'; // Add Subject import
import { DocumentService } from '../../services/document.service';
import { DocumentSearchService } from '../../services/document-search.service';
import { DetailsDialogueComponent } from './details/details-dialogue.component';
import { DeleteDialog } from './delete/delete-dialogue.component';

describe('DocumentListComponent', () => {
  let component: DocumentListComponent;
  let fixture: ComponentFixture<DocumentListComponent>;
  let dialogSpy: jasmine.SpyObj<MatDialog>;
  let snackBarSpy: jasmine.SpyObj<MatSnackBar>;
  let docServiceSpy: jasmine.SpyObj<DocumentService>;
  let searchServiceSpy: jasmine.SpyObj<DocumentSearchService>;

  beforeEach(async () => {
    dialogSpy = jasmine.createSpyObj('MatDialog', ['open']);
    snackBarSpy = jasmine.createSpyObj('MatSnackBar', ['open']);
    docServiceSpy = jasmine.createSpyObj('DocumentService', ['listDocuments', 'deleteDocument'], { hostUrl: 'http://localhost' });
    searchServiceSpy = jasmine.createSpyObj('DocumentSearchService', [], { results$: of(null) });

    docServiceSpy.listDocuments.and.returnValue(of([]));
    docServiceSpy.deleteDocument.and.returnValue(of({ message: 'deleted' }));
    // Add this line:
    docServiceSpy.refresh$ = of(false); // or use new Subject<boolean>() if you want to emit later

    await TestBed.configureTestingModule({
      imports: [DocumentListComponent],
    }).compileComponents();

    await TestBed.overrideComponent(DocumentListComponent, {
      set: {
        providers: [
          { provide: MatDialog, useValue: dialogSpy },
          { provide: MatSnackBar, useValue: snackBarSpy },
          { provide: DocumentService, useValue: docServiceSpy },
          { provide: DocumentSearchService, useValue: searchServiceSpy }
        ]
      }
    });

    fixture = TestBed.createComponent(DocumentListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should open details dialog with correct data', () => {
    const doc = { id: 1, filename: 'file.pdf', filetype: 'application/pdf', filepath: '/file.pdf', uploaded_at: '', relevance: 1, highlight: '' };
    dialogSpy.open.and.returnValue({} as any);

    component.openDocumentDialog(doc as any);

    expect(dialogSpy.open).toHaveBeenCalledWith(DetailsDialogueComponent, jasmine.objectContaining({
      width: '500px',
      data: jasmine.objectContaining({ filename: 'file.pdf', host: 'http://localhost' })
    }));
  });

  it('should open delete dialog and call deleteDocument on confirm', fakeAsync(() => {
    const doc = { id: 1, filename: 'file.pdf', filetype: 'application/pdf', filepath: '/file.pdf', uploaded_at: '', relevance: 1, highlight: '' };
    const afterClosed$ = of(true);
    dialogSpy.open.and.returnValue({ afterClosed: () => afterClosed$ } as any);
    spyOn(component, 'deleteDocument');

    component.confirmDelete(doc as any);
    tick();

    expect(dialogSpy.open).toHaveBeenCalledWith(DeleteDialog, jasmine.objectContaining({
      width: '500px',
      data: { filename: 'file.pdf' }
    }));
    expect(component.deleteDocument).toHaveBeenCalledWith(doc as any);
  }));

  it('should not call deleteDocument if delete dialog is cancelled', fakeAsync(() => {
    const doc = { id: 1, filename: 'file.pdf', filetype: 'application/pdf', filepath: '/file.pdf', uploaded_at: '', relevance: 1, highlight: '' };
    const afterClosed$ = of(false);
    dialogSpy.open.and.returnValue({ afterClosed: () => afterClosed$ } as any);
    spyOn(component, 'deleteDocument');

    component.confirmDelete(doc as any);
    tick();

    expect(component.deleteDocument).not.toHaveBeenCalled();
  }));

  it('should remove document and show snackbar on successful delete', () => {
    const doc = { id: 1, filename: 'file.pdf', filetype: 'application/pdf', filepath: '/file.pdf', uploaded_at: '', relevance: 1, highlight: '' };
    component.documents = [doc as any];
    docServiceSpy.deleteDocument.and.returnValue(of({ message: 'deleted' }));

    component.deleteDocument(doc as any);

    expect(component.documents.length).toBe(0);
    expect(snackBarSpy.open).toHaveBeenCalledWith('File deleted successfully', 'Close', { duration: 3000 });
  });

  it('should show snackbar on delete error', () => {
    const doc = { id: 1, filename: 'file.pdf', filetype: 'application/pdf', filepath: '/file.pdf', uploaded_at: '', relevance: 1, highlight: '' };
    component.documents = [doc as any];
    docServiceSpy.deleteDocument.and.returnValue(throwError(() => new Error('fail')));

    component.deleteDocument(doc as any);

    expect(snackBarSpy.open).toHaveBeenCalledWith('Failed to delete file', 'Close', { duration: 3000 });
  });
});
