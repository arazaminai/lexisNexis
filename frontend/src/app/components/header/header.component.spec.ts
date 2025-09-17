import { ComponentFixture, TestBed } from '@angular/core/testing';
import { HeaderComponent } from './header.component';
import { MatDialog } from '@angular/material/dialog';
import { of } from 'rxjs';
import { DocumentService } from '../../services/document.service';

describe('HeaderComponent', () => {
  let component: HeaderComponent;
  let fixture: ComponentFixture<HeaderComponent>;
  let dialogSpy: jasmine.SpyObj<MatDialog>;
  let docServiceSpy: jasmine.SpyObj<DocumentService>;

  beforeEach(async () => {
    dialogSpy = jasmine.createSpyObj('MatDialog', ['open', 'closeAll']);
    docServiceSpy = jasmine.createSpyObj('DocumentService', ['searchDocuments', 'setRefresh']);

    await TestBed.configureTestingModule({
      imports: [HeaderComponent],
      providers: [
        { provide: MatDialog, useValue: dialogSpy },
        { provide: DocumentService, useValue: docServiceSpy }
      ]
    }).compileComponents();

    fixture = TestBed.createComponent(HeaderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should open upload dialog and handle uploadComplete', () => {
    // Mock dialogRef with componentInstance.uploadComplete as observable
    const uploadComplete$ = of();
    const dialogRefMock = {
      componentInstance: {
        uploadComplete: uploadComplete$
      }
    };
    dialogSpy.open.and.returnValue(dialogRefMock as any);

    component.openUploadDialog();

    expect(dialogSpy.open).toHaveBeenCalled();
  });

  it('should close all dialogs and refresh on uploadComplete', () => {
    let uploadCompleteHandler: Function | undefined;
    const dialogRefMock = {
      componentInstance: {
        uploadComplete: {
          subscribe: (fn: Function) => { uploadCompleteHandler = fn; }
        }
      }
    };
    dialogSpy.open.and.returnValue(dialogRefMock as any);

    component.openUploadDialog();

    // Simulate uploadComplete event
    if (uploadCompleteHandler) uploadCompleteHandler();

    expect(dialogSpy.closeAll).toHaveBeenCalled();
    expect(docServiceSpy.setRefresh).toHaveBeenCalledWith(true);
  });
});
