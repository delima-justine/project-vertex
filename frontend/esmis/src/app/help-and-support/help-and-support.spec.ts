import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HelpAndSupport } from './help-and-support';

describe('HelpAndSupport', () => {
  let component: HelpAndSupport;
  let fixture: ComponentFixture<HelpAndSupport>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HelpAndSupport]
    })
    .compileComponents();

    fixture = TestBed.createComponent(HelpAndSupport);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
